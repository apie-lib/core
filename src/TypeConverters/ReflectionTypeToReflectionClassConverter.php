<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\Exceptions\CanNotConvertObjectException;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionType;

/**
 * @implements ConverterInterface<ReflectionType, ReflectionClass|null>
 */
class ReflectionTypeToReflectionClassConverter implements ConverterInterface
{
    /**
     * @return ReflectionClass<object>|null
     */
    public function convert(ReflectionType $input, ?ReflectionType $wantedType = null): ?ReflectionClass
    {
        if ($input instanceof ReflectionNamedType && !$input->isBuiltin()) {
            return new ReflectionClass($input->getName());
        }
        if (!$wantedType || !$wantedType->allowsNull()) {
            throw new CanNotConvertObjectException($input, $wantedType ?? ReflectionTypeFactory::createReflectionType('mixed'));
        }

        return null;
      
        
    }
}
