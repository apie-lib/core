<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\ReflectionTypeFactory;
use Apie\TypeConverter\TypeConverter;
use ReflectionClass;
use ReflectionMethod;
use ReflectionType;

/**
 * @implements ConverterInterface<ReflectionClass, ReflectionType>
 */
class ReflectionClassToReflectionTypeConverter implements ConverterInterface
{
    /**
     * @param ReflectionClass<object> $input
     */
    public function convert(ReflectionClass $input, ?ReflectionType $wantedType = null, TypeConverter $converter = null): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType($input->name);
    }
}
