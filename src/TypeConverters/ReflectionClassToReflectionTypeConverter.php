<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionType;

/**
 * @implements ConverterInterface<ReflectionClass, ReflectionType>
 */
class ReflectionClassToReflectionTypeConverter implements ConverterInterface
{
    /**
     * @param ReflectionClass<object> $input
     */
    public function convert(ReflectionClass $input): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType($input->name);
    }
}
