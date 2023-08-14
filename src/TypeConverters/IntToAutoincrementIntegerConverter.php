<?php
namespace Apie\Core\TypeConverters;

use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\TypeConverter;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

/**
 * @implements ConverterInterface<int, AutoIncrementInteger>
 */
class IntToAutoincrementIntegerConverter implements ConverterInterface
{
    public function convert(int $integer, ReflectionType $wantedType, TypeConverter $typeConverter): AutoIncrementInteger
    {
        $class = AutoIncrementInteger::class;
        if ($wantedType instanceof ReflectionNamedType) {
            $class = $wantedType->getName();
        } elseif ($wantedType instanceof ReflectionUnionType) {
            foreach ($wantedType->getTypes() as $subType) {
                if ($subType instanceof ReflectionNamedType) {
                    $classToCheck = $typeConverter->convertTo($subType, ReflectionClass::class);
                    if ($classToCheck instanceof ReflectionClass && $classToCheck->implementsInterface(AutoincrementInteger::class)) {
                        $class = $classToCheck->name;
                    }
                }
            }
        }
        return $class::fromNative($integer);
    }
}
