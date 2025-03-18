<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;
use ReflectionNamedType;
use Stringable;

class FromValueObject implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        $refl = new ReflectionClass($object);
        if (!$refl->implementsInterface(ValueObjectInterface::class)) {
            return false;
        }
        $type = $refl->getMethod('toNative')->getReturnType();
        if ($type instanceof ReflectionNamedType) {
            return $type->getName() === 'string' || $type->getName() === '?string';
        }

        return false;
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $object, ApieContext $context, Indexer $indexer): array
    {
        assert($object instanceof ValueObjectInterface);
        assert($object instanceof Stringable);
        $value = (string) $object;
        return [$value => 1];
    }
}
