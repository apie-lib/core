<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\ConstructorParameter;
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
            $list[$property->getName()] = new PublicProperty($property, true);
        }
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(set).+$/i', $method->name) && !$method->isStatic() && !$method->isAbstract()) {
                $list[lcfirst(substr($method->name, 3))] = new SetterMethod($method);
            }
        }

        return new CompositeMetadata((new MetadataFieldHashmap($list))->sort());
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        foreach ($this->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadOnly() && !$property->isPromoted()) {
                continue;
            }
            $list[$property->getName()] = new PublicProperty($property);
        }
        foreach ($this->class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (preg_match('/^(set).+$/i', $method->name) && !$method->isStatic() && !$method->isAbstract()) {
                $list[lcfirst(substr($method->name, 3))] = new SetterMethod($method);
            }
        }

        $constructor = $this->class->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                $list[$parameter->name] = new ConstructorParameter($parameter);
            }
        }

        return new CompositeMetadata((new MetadataFieldHashmap($list))->sort());
    }
}
