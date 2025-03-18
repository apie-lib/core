<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Metadata\ValueObjectMetadata;
use Apie\Core\Utils\ValueObjectUtils;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

final class ValueObjectStrategy implements StrategyInterface
{
    /**
     * @param ReflectionClass<ValueObjectInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public static function supports(ReflectionClass $class): bool
    {
        return ValueObjectUtils::isNonCompositeValueObject($class);
    }

    public function getCreationMetadata(ApieContext $context): ValueObjectMetadata
    {
        return new ValueObjectMetadata($this->class);
    }

    public function getModificationMetadata(ApieContext $context): ValueObjectMetadata
    {
        return $this->getCreationMetadata($context);
    }

    public function getResultMetadata(ApieContext $context): ValueObjectMetadata
    {
        return $this->getCreationMetadata($context);
    }
}
