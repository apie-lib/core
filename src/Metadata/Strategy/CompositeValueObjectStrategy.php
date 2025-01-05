<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\PublicProperty;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Utils\ValueObjectUtils;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

final class CompositeValueObjectStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return ValueObjectUtils::isCompositeValueObject($class);
    }

    /**
     * @param ReflectionClass<ValueObjectInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    private function getMetadata(ApieContext $context, bool $setterHooks): CompositeMetadata
    {
        $method = $this->class->getMethod('getFields');
        $map = [];
        foreach ($method->invoke(null) as $name => $field) {
            $prop = $this->class->getProperty($name);
            $map[$name] = new PublicProperty($prop, $field->isOptional(), $setterHooks);
        }
        return new CompositeMetadata(new MetadataFieldHashmap($map), $this->class);
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->getMetadata($context, true);
    }

    public function getModificationMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->getMetadata($context, true);
    }

    public function getResultMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->getMetadata($context, false);
    }
}
