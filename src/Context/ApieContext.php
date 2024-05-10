<?php
namespace Apie\Core\Context;

use Apie\Common\ContextConstants;
use Apie\Core\Attributes\ApieContextAttribute;
use Apie\Core\Attributes\Internal;
use Apie\Core\Attributes\RuntimeCheck;
use Apie\Core\Attributes\StaticCheck;
use Apie\Core\Entities\EntityWithStatesInterface;
use Apie\Core\Exceptions\ActionNotAllowedException;
use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Metadata\Concerns\UseContextKey;
use Apie\Core\Utils\EntityUtils;
use LogicException;
use ReflectionClass;
use ReflectionEnumUnitCase;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;
use Throwable;

/**
 * ApieContext is used as builder/mediator and passed though many Apie functions. It can be used to filter (for example
 * only show property when authenticated) or can be used to provide extra functionality to other methods.
 */
final class ApieContext
{
    use UseContextKey;

    /** @var array<int, class-string<ApieContextAttribute>> */
    private const ATTRIBUTES = [
        StaticCheck::class
    ];
    /** @var array<string, \Closure> */
    private array $predefined;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(private array $context = [])
    {
        $this->predefined = [
            ApieContext::class => function () {
                return $this;
            }
        ];
    }

    public function withContext(string $key, mixed $value): self
    {
        $instance = clone $this;
        $instance->context[$key] = $value;
        return $instance;
    }

    public function hasContext(string $key): bool
    {
        return array_key_exists($key, $this->context) || isset($this->predefined[$key]);
    }

    public function getContext(string $key, bool $throwError = true): mixed
    {
        if (isset($this->predefined[$key])) {
            return $this->predefined[$key]();
        }
        if (!array_key_exists($key, $this->context)) {
            if (!$throwError) {
                return null;
            }
            throw new IndexNotFoundException($key);
        }

        return $this->context[$key];
    }

    private function registerOrMarkAmbiguous(string $offset, object $instance): void
    {
        if (!isset($this->context[$offset])) {
            $this->context[$offset] = $instance;
            return;
        }
        if ($this->context[$offset] instanceof AmbiguousCall) {
            $this->context[$offset] = $this->context[$offset]->withAddedName(get_class($instance));
        } else {
            $this->context[$offset] = new AmbiguousCall($offset, get_class($this->context[$offset]), get_class($instance));
        }
    }

    public function registerInstance(object $object): self
    {
        $refl = new ReflectionClass($object);

        $instance = $this->withContext($refl->name, $object);
        foreach ($refl->getInterfaceNames() as $interface) {
            $instance->registerOrMarkAmbiguous($interface, $object);
        }
        $refl = $refl->getParentClass();
        while ($refl) {
            $instance->registerOrMarkAmbiguous($refl->name, $object);
            $refl = $refl->getParentClass();
        }

        return $instance;
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function getApplicableGetters(ReflectionClass $class, bool $runtimeChecks = true): ReflectionHashmap
    {
        $list = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($this->appliesToContext($property, $runtimeChecks)) {
                $list[$property->getName()] = $property;
            }
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(get|has|is).+$/i', $method->name) && $this->appliesToContext($method, $runtimeChecks) && !$method->isStatic() && !$method->isAbstract()) {
                if (strpos($method->name, 'is') === 0) {
                    $list[lcfirst(substr($method->name, 2))] = $method;
                } else {
                    $list[lcfirst(substr($method->name, 3))] = $method;
                }
            }
        }
        return new ReflectionHashmap($list);
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function getApplicableSetters(ReflectionClass $class, bool $runtimeChecks = true): ReflectionHashmap
    {
        $list = [];
        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadOnly()) {
                continue;
            }
            if ($this->appliesToContext($property, $runtimeChecks)) {
                $list[$property->getName()] = $property;
            }
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(set).+$/i', $method->name) && $this->appliesToContext($method, $runtimeChecks) && !$method->isStatic() && !$method->isAbstract()) {
                $list[lcfirst(substr($method->name, 3))] = $method;
            }
        }
        return new ReflectionHashmap($list);
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function getApplicableMethods(ReflectionClass $class, bool $runtimeChecks = true): ReflectionHashmap
    {
        $list = [];
        $filter = function (ReflectionMethod $method) {
            return !preg_match('/^(__|create|set|get|has|is).+$/i', $method->name);
        };
        if ($runtimeChecks && $this->hasContext(ContextConstants::RESOURCE)) {
            $resource = $this->getContext(ContextConstants::RESOURCE);
            if ($resource instanceof EntityWithStatesInterface) {
                $allowedMethods = $resource->provideAllowedMethods()->toArray();
                $allowedMethodsMap = array_combine($allowedMethods, $allowedMethods);
                $filter = function (ReflectionMethod $method) use (&$allowedMethodsMap) {
                    return isset($allowedMethodsMap[$method->name]);
                };
            }
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->appliesToContext($method, $runtimeChecks) && $filter($method)) {
                $list[$method->name] = $method;
            }
        }
        return new ReflectionHashmap($list);
    }

    /**
     * @param ReflectionClass<object>|ReflectionMethod|ReflectionProperty|ReflectionType|ReflectionEnumUnitCase $method
     */
    public function appliesToContext(ReflectionClass|ReflectionMethod|ReflectionProperty|ReflectionType|ReflectionEnumUnitCase $method, bool $runtimeChecks = true, ?Throwable $errorToThrow = null): bool
    {
        if ($method->getAttributes(Internal::class)) {
            return false;
        }
        $attributesToCheck = $runtimeChecks ? [RuntimeCheck::class, ...self::ATTRIBUTES] : self::ATTRIBUTES;
        foreach ($attributesToCheck as $attribute) {
            foreach ($method->getAttributes($attribute) as $reflAttribute) {
                if (!$reflAttribute->newInstance()->applies($this)) {
                    if ($errorToThrow) {
                        throw $errorToThrow;
                    }
                    return false;
                }
            }
        }
        if ($method instanceof ReflectionMethod && $runtimeChecks) {
            foreach (EntityUtils::getContextParameters($method) as $parameter) {
                if ($parameter->isDefaultValueAvailable()) {
                    continue;
                }
                $key = $this->getContextKey($this, $parameter);
                if ($key === null) {
                    if ($errorToThrow) {
                        throw new LogicException(
                            'Parameter ' . $parameter->name . ' has an invalid context key',
                            0,
                            $errorToThrow
                        );
                    }
                    return false;
                }
                if (!isset($this->context[$key]) && !isset($this->predefined[$key])) {
                    if ($errorToThrow) {
                        throw new IndexNotFoundException($key);
                    }
                    return false;
                }
            }
        }
        return true;
    }

    public function checkAuthorization(): void
    {
        try {
            if (!$this->isAuthorized(runtimeChecks: true, throwError: true)) {
                throw new ActionNotAllowedException();
            }
        } catch (ActionNotAllowedException) {
            throw new ActionNotAllowedException();
        } catch (Throwable $error) {
            throw new ActionNotAllowedException($error);
        }
    }

    public function isAuthorized(bool $runtimeChecks, bool $throwError = false): bool
    {
        $actionClass = $this->getContext(ContextConstants::APIE_ACTION, $throwError);
        if (!$actionClass) {
            return true;
        }
        return $actionClass::isAuthorized($this, $runtimeChecks, $throwError);
    }
}
