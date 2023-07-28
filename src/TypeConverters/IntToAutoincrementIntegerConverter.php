<?php
namespace Apie\Core\TypeConverters;

use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\TypeConverter;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

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
                    if ($classToCheck instanceof AutoincrementInteger) {
                        $class = $classToCheck;
                    }
                }
            }
        }
        return $class::fromNative($integer);
    }
}