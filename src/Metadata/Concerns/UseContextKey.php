<?php
namespace Apie\Core\Metadata\Concerns;

use Apie\Core\Attributes\Context;
use Apie\Core\Context\ApieContext;
use Apie\Core\Exceptions\IndexNotFoundException;
use Apie\Core\Exceptions\InvalidTypeException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

trait UseContextKey
{
    /**
     * @phpstan-return ($returnNull is true ? string|null : string)
     */
    private function getContextKey(ApieContext $apieContext, ReflectionParameter $parameter, bool $returnNull = true): ?string
    {
        foreach ($parameter->getAttributes(Context::class) as $attribute) {
            $contextKey = $attribute->newInstance()->contextKey;
            if (null !== $contextKey) {
                return $contextKey;
            }
        }
        $type = $parameter->getType();
        if ($type === null || ($type instanceof ReflectionNamedType && $type->isBuiltin())) {
            return $parameter->name;
        }

        return $this->getContextKeyForType($apieContext, $type);
    }

    private function getContextKeyForType(ApieContext $apieContext, ReflectionType $type, bool $returnNull = true): ?string
    {
        if ($type instanceof ReflectionNamedType) {
            return $type->getName();
        }
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $subtype) {
                $key = $this->getContextKeyForType($apieContext, $subtype);
                if ($key !== null) {
                    return $key;
                }
            }
            if ($returnNull) {
                return null;
            }
            throw new IndexNotFoundException((string) $type);
        }
        throw new InvalidTypeException($type, 'ReflectionNamedType|ReflectionUnionType');
    }
}
