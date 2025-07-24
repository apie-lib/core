<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Attributes\Internal;
use Apie\Core\Attributes\Optional;
use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\ConstructorParameter;
use Apie\Core\Metadata\Fields\GetterMethod;
use Apie\Core\Metadata\Fields\PublicProperty;
use Apie\Core\Metadata\Fields\SetterMethod;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class RegularObjectStrategy implements StrategyInterface
{
    /**
     * @param ReflectionClass<object> $class
     */
    public static function supports(ReflectionClass $class): bool
    {
        return $class->isInstantiable() || $class->isInterface();
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getModificationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        foreach ($this->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadOnly() || !$context->appliesToContext($property, false)) {
                continue;
            }
            $list[$property->getName()] = new PublicProperty($property, true, true);
        }
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(set).+$/i', $method->name) && !$method->isStatic() && !$method->isAbstract() && $context->appliesToContext($method, false)) {
                $list[lcfirst(substr($method->name, 3))] = new SetterMethod($method);
            }
        }

        return new CompositeMetadata((new MetadataFieldHashmap($list))->sort(), $this->class);
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        foreach ($this->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (($property->isReadOnly() && !$property->isPromoted()) || !$context->appliesToContext($property, false)) {
                continue;
            }
            $optional = false;
            if (PHP_VERSION_ID >= 80400) {
                $optional = !empty($property->getHooks());
            }
            $list[$property->getName()] = new PublicProperty($property, $optional, true);
        }
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(set).+$/i', $method->name) && !$method->isStatic() && !$method->isAbstract() && $context->appliesToContext($method, false)) {
                $list[lcfirst(substr($method->name, 3))] = new SetterMethod($method);
            }
        }

        $constructor = $this->class->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $list[$parameter->name] = new ConstructorParameter($parameter);
            }
        }

        return new CompositeMetadata((new MetadataFieldHashmap($list))->sort(), $this->class);
    }

    public function getResultMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        foreach ($this->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->getAttributes(Internal::class) && $context->appliesToContext($property, false)) {
                $list[$property->getName()] = new PublicProperty(
                    $property,
                    !empty($property->getAttributes(Optional::class)),
                    false
                );
            }
        }
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(get|is|has).+$/i', $method->name) && !$method->isStatic() && !$method->isAbstract() && !$method->getAttributes(Internal::class)) {
                $list[lcfirst(substr($method->name, str_starts_with($method->name, 'is') ? 2 : 3))] = new GetterMethod($method);
            }
        }

        return new CompositeMetadata((new MetadataFieldHashmap($list))->sort(), $this->class);
    }
}
