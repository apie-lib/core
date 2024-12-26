<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ReflectionType;

/**
 * @template T
 * @implements ConverterInterface<array<int, T>, Collection<int, T>>
 */
class ArrayToDoctrineCollection implements ConverterInterface
{
    /**
     * @param array<int, T> $input
     * @return Collection<int, T>
     */
    public function convert(array $input): Collection
    {
        return new ArrayCollection($input);
    }
}
