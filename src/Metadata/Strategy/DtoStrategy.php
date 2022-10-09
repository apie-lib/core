<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Attributes\Optional;
use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Dto\DtoInterface;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;
use ReflectionProperty;

final class DtoStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->isInstantiable() && $class->implementsInterface(DtoInterface::class);
    }

    /**
     * @param ReflectionClass<DtoInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        $required = [];
        foreach ($this->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadOnly()) {
                continue;
            }
            $list[$property->getName()] = $property;
            if (!$property->getAttributes(Optional::class)) {
                $required[$property->getName()] = $property->getName();
            }
        }
    
        return new CompositeMetadata(new ReflectionHashmap($list), new StringList($required));
    }
}
