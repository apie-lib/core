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
 * @implements ConverterInterface<ReflectionMethod, ReflectionClass>
 */
class ReflectionMethodToReflectionClassConverter implements ConverterInterface
{
    /**
     * @return ReflectionClass<object>|null
     */
    public function convert(ReflectionMethod $input, ?ReflectionType $wantedType = null, TypeConverter $converter = null): ?ReflectionClass
    {
        $wantedType ??= ReflectionTypeFactory::createReflectionType(ReflectionClass::class);
        if (str_starts_with($input->getName(), 'set') && $input->getNumberOfRequiredParameters() > 0) {
            $arguments = $input->getParameters();
            return $converter->convertTo(reset($arguments), $wantedType);
        }
        foreach (['has', 'is', 'get'] as $methodPrefix) {
            if (str_starts_with($input->getName(), $methodPrefix)) {
                return $converter->convertTo($input->getReturnType(), $wantedType);
            }
        }
        foreach (['toNative', 'offsetGet'] as $methodName) {
            if ($input->getName() === $methodName) {
                return $converter->convertTo($input->getReturnType(), $wantedType);
            }
        }

        if (!$wantedType->allowsNull()) {
            throw new CanNotConvertObjectException($input, $wantedType);
        }

        return null;
    }
}
