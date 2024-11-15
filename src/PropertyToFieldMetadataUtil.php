<?php
namespace Apie\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ReflectionTypeSet;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\MetadataFactory;
use Apie\Core\Metadata\MetadataInterface;
use Apie\Core\Utils\ConverterUtils;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class PropertyToFieldMetadataUtil
{
    private function __construct()
    {
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public static function fromPropertyStringToFieldMetadata(
        ReflectionClass $class,
        ApieContext $apieContext,
        string $property
    ): ?FieldInterface {
        return self::fromPropertyArrayToFieldMetadata($class, $apieContext, explode('.', $property));
    }

    /**
     * @param ReflectionClass<object> $class
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

    public static function hasPropertyWithType(
        ReflectionType $input,
        ReflectionNamedType $searchType,
        ApieContext $apieContext,
        ReflectionTypeSet $visitedTypes = new ReflectionTypeSet()
    ): bool
    {
        if (isset($visitedTypes[$input])) {
            return false;
        }
        $visitedTypes[] = $input;
        if ($input instanceof ReflectionUnionType || $input instanceof ReflectionIntersectionType) {
            foreach ($input->getTypes() as $type) {
                if (self::hasPropertyWithType($type, $searchType, $apieContext, $visitedTypes->clone())) {
                    return true;
                }
            }
            return false;
        }
        assert($input instanceof ReflectionNamedType);
        if ($input->isBuiltin()) {
            switch ($input->getName()) {
                case 'null':
                    return $searchType->allowsNull();
                case 'int':
                    return in_array($searchType->getName(), ['int', 'float']);
                case 'true':
                case 'false':
                    return in_array($searchType->getName(), [$input->getName(), 'bool']);
                case 'bool':
                    return in_array($searchType->getName(), ['true', 'false', 'bool']);
                case 'array':
                    return in_array($searchType->getName(), ['string', 'true', 'false', 'bool', 'int', 'float', 'null']);
                default:
                    return $searchType->getName() === $input->getName();
            }
        }
        $class = ConverterUtils::toReflectionClass($input);
        if ($class === null) {
            return false;
        }
        if (in_array($searchType->getName(), $class->getInterfaceNames())) {
            return true;
        }
        $ptr = $class;
        while ($ptr) {
            if ($class->name === $searchType->getName()) {
                return true;
            }
            $ptr = $ptr->getParentClass();
        }
        $metadata = MetadataFactory::getResultMetadata($class, $apieContext);
        $hashmap = $metadata->getHashmap();
        foreach ($hashmap as $getter) {
            $typehint = $getter->getTypehint();
            if (!$typehint) {
                return true;
            }
            if (self::hasPropertyWithType($typehint, $searchType, $apieContext, $visitedTypes->clone())) {
                return true;
            }
        }
        $arrayItemType = ConverterUtils::toReflectionType($metadata->getArrayItemType()?->toClass() ?? 'null');
        if ($arrayItemType) {
            return self::hasPropertyWithType($arrayItemType, $searchType, $apieContext, $visitedTypes->clone());
        }

        return false;
    }

    /**
     * @param array<int, string> $property
     */
    private static function visit(MetadataInterface $node, ApieContext $apieContext, array $property): ?FieldInterface
    {
        $hashmap = $node->getHashmap();
        $key = array_shift($property);
        if (isset($hashmap[$key])) {
            if (empty($property)) {
                return $hashmap[$key];
            }
            $type = $hashmap[$key]->getTypehint();
            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $class = new ReflectionClass($type->getName());
                return self::visit(
                    MetadataFactory::getCreationMetadata($class, $apieContext),
                    $apieContext,
                    $property
                );
            }
        }
        $arrayItemType = $node->getArrayItemType();
        if (null === $arrayItemType) {
            return null;
        }
        return self::visit($arrayItemType, $apieContext, $property);
    }
}
