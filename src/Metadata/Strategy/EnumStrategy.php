<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\EnumMetadata;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;
use ReflectionEnum;
use UnitEnum;

final class EnumStrategy implements StrategyInterface
{
    private ReflectionEnum $enum;

    public static function supports(ReflectionClass $class): bool
    {
        return $class->isEnum();
    }

    /**
     * @param ReflectionClass<UnitEnum> $class
     */
    public function __construct(ReflectionClass $class)
    {
        $this->enum = new ReflectionEnum($class->name);
    }

    public function getCreationMetadata(ApieContext $context): EnumMetadata
    {
        return new EnumMetadata($this->enum);
    }

    public function getModificationMetadata(ApieContext $context): EnumMetadata
    {
        return new EnumMetadata($this->enum);
    }
}
