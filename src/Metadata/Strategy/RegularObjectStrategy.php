<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Attributes\Context;
use Apie\Core\Attributes\Optional;
use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\CompositeMetadata;
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
        return $class->isInstantiable();
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
            if ($property->isReadOnly()) {
                continue;
            }
            $list[$property->getName()] = $property;
        }
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(set).+$/i', $method->name) && !$method->isStatic() && !$method->isAbstract()) {
                $list[lcfirst(substr($method->name, 3))] = $method;
            }
        }

        return new CompositeMetadata((new ReflectionHashmap($list))->sort(), new StringList([]));
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        $required = [];
        foreach ($this->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadOnly() && !$property->isPromoted()) {
                continue;
            }
            $list[$property->getName()] = $property;
            if (!$property->getAttributes(Optional::class)) {
                $required[$property->getName()] = $property->getName();
            }
        }
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(set).+$/i', $method->name) && !$method->isStatic() && !$method->isAbstract()) {
                $list[lcfirst(substr($method->name, 3))] = $method;
            }
        }

        $constructor = $this->class->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $list[$parameter->name] = $parameter;
                if ($parameter->isOptional() || $parameter->getAttributes(Context::class)) {
                    // this is possible if you have a promoted public property in the constructor....
                    // it is also possible for constructor arguments with @Context attribute on it.
                    unset($required[$parameter->name]);
                } else {
                    $required[$parameter->name] = $parameter->name;
                }
            }
        }

        return new CompositeMetadata((new ReflectionHashmap($list))->sort(), new StringList($required));
    }
}
