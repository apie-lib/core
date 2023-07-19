<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Dto\DtoInterface;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\PublicProperty;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Utils\DtoUtils;
use ReflectionClass;
use ReflectionProperty;

final class DtoStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return DtoUtils::isDto($class);
    }

    /**
     * @param ReflectionClass<DtoInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    private function getDtoMetadata(ApieContext $context, bool $optional): CompositeMetadata
    {
        $list = [];
        foreach ($this->class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadOnly()) {
                if ($optional || !$property->isPromoted()) {
                    continue;
                }
            }
            $list[$property->getName()] = new PublicProperty($property, $optional);
        }
    
        return new CompositeMetadata(new MetadataFieldHashmap($list), $this->class);
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->getDtoMetadata($context, false);
    }

    public function getModificationMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->getDtoMetadata($context, true);
    }

    public function getResultMetadata(ApieContext $context): CompositeMetadata
    {
        return $this->getDtoMetadata($context, false);
    }
}
