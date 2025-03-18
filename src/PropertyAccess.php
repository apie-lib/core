<?php
namespace Apie\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;
use ReflectionClass;

final class PropertyAccess
{
    private function __construct()
    {
    }

    /**
     * @param array<int, string> $property
     */
    public static function getPropertyValue(
        object $object,
        array $property,
        ApieContext $context = new ApieContext(),
        bool $throwErrorOnMissing = true
    ): mixed {
        $node = MetadataFactory::getResultMetadata(new ReflectionClass($object), $context);

        return self::visit($object, $node, $context, $property, $throwErrorOnMissing);
    }

    /**
     * @param array<int, string> $property
     */
    private static function visit(
        mixed $object,
        MetadataInterface $node,
        ApieContext $apieContext,
        array $property,
        bool $throwErrorOnMissing
    ): mixed {
        $hashmap = $node->getHashmap();
        $key = array_shift($property);
        if (($hashmap[$key] ?? null) instanceof GetterInterface) {
            $newObject = $hashmap[$key]->getValue($object, $apieContext);
            if (empty($property)) {
                return $newObject;
            }
            if (!is_object($newObject)) {
                return self::throwErrorOnMissingValue($key, $throwErrorOnMissing);
            }
            $class = new ReflectionClass($newObject);
            return self::visit(
                $newObject,
                MetadataFactory::getResultMetadata($class, $apieContext),
                $apieContext,
                $property,
                $throwErrorOnMissing
            );
        }
        $arrayItemType = $node->getArrayItemType();
        if (null === $arrayItemType) {
            return self::throwErrorOnMissingValue($key, $throwErrorOnMissing);
        }
        if (is_array($object) || $object instanceof ItemList || $object instanceof ItemHashmap) {
            if (!isset($object[$key])) {
                return self::throwErrorOnMissingValue($key, $throwErrorOnMissing);
            }
            $value = $object[$key];
            if (empty($property)) {
                return $value;
            }
            return self::visit($value, $arrayItemType, $apieContext, $property, $throwErrorOnMissing);
        }
        return self::throwErrorOnMissingValue($key, $throwErrorOnMissing);
    }

    /**
     * @phpstan-return ($throwErrorOnMissing is true ? never : null)
     */
    private static function throwErrorOnMissingValue(?string $key, bool $throwErrorOnMissing): mixed
    {
        if ($throwErrorOnMissing) {
            throw new IndexNotFoundException($key);
        }
        return null;
    }
}
