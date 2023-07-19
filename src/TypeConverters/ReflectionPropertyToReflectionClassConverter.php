<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\TypeConverter;
use ReflectionClass;
use ReflectionProperty;
use ReflectionType;

/**
 * @implements ConverterInterface<ReflectionProperty, ReflectionClass>
 */
class ReflectionPropertyToReflectionClassConverter implements ConverterInterface
{
    /**
     * @return ReflectionClass<object>|null
     */
    public function convert(ReflectionProperty $input, ?ReflectionType $wantedType = null, TypeConverter $converter = null): ?ReflectionClass
    {
        $wantedType ??= ReflectionTypeFactory::createReflectionType('ReflectionClass');
        return $converter->convertTo(
            $input->getType() ?? ReflectionTypeFactory::createReflectionType('mixed'),
            $wantedType
        );
    }
}
