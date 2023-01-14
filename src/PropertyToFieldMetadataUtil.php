<?php
namespace Apie\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;
use ReflectionClass;

final class PropertyToFieldMetadataUtil
{
    private function __construct()
    {
    }

    public static function fromPropertyStringToFieldMetadata(
        ReflectionClass $class,
        ApieContext $apieContext,
        string $property
    ): ?FieldInterface {
        return self::fromPropertyArrayToFieldMetadata($class, $apieContext, explode('.', $property));
    }

    /**
     * @param array<int, string> $property
     */
    public static function fromPropertyArrayToFieldMetadata(
        ReflectionClass $class,
        ApieContext $apieContext,
        array $property
    ): ?FieldInterface {
        $root = $apieContext->hasContext('id')
            ? MetadataFactory::getModificationMetadata($class, $apieContext)
            : MetadataFactory::getCreationMetadata($class, $apieContext);
        return self::visit($root, $apieContext, $property);
    }

    /**
     * @param array<int, string> $property
     */
    private static function visit(MetadataInterface $node, ApieContext $apieContext, array $property): ?FieldInterface
    {
        $hashmap = $node->getHashmap();
        $key = array_shift($property);

        if (isset($hashmap[$key]) && empty($property)) {
            return $hashmap[$key];
        }
        $arrayItemType = $node->getArrayItemType();
        if (null === $arrayItemType) {
            return null;
        }
        return self::visit($arrayItemType, $apieContext, $property);
    }
}
