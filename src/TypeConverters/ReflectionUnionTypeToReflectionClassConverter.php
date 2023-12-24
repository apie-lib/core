<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use Apie\TypeConverter\TypeConverter;
use ReflectionClass;
use ReflectionType;
use ReflectionUnionType;

/**
 * @implements ConverterInterface<ReflectionUnionType, ReflectionClass|null>
 */
class ReflectionUnionTypeToReflectionClassConverter implements ConverterInterface
{
    /**
     * @return ReflectionClass<object>|null
     */
    public function convert(ReflectionUnionType $input, ?ReflectionType $wantedType = null, ?TypeConverter $typeConverter = null): ?ReflectionClass
    {
        assert($typeConverter !== null);
        foreach ($input->getTypes() as $type) {
            $class = $typeConverter->convertTo($type, $wantedType);
            if ($class) {
                return $class;
            }
        }

        return null;
      
        
    }
}
