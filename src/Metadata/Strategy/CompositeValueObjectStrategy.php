<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\Typehint;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\ReflectionTypeFactory;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

final class CompositeValueObjectStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(ValueObjectInterface::class)
            && in_array(CompositeValueObject::class, $class->getTraitNames());
    }

    /**
     * @param ReflectionClass<ValueObjectInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $method = $this->class->getMethod('getFields');
        $map = [];
        foreach ($method->invoke(null) as $name => $field) {
            $map[$name] = new Typehint(
                ReflectionTypeFactory::createReflectionType($field->getTypehint()),
                !$field->isOptional()
            );
        }
        return new CompositeMetadata(new MetadataFieldHashmap($map));
    }

    public function getModificationMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->getCreationMetadata($context);
    }
}
