<?php
namespace Apie\Core\TypeConverters;

use Apie\TypeConverter\ConverterInterface;
use Doctrine\Common\Collections\Collection;

/**
 * @template T
 * @implements ConverterInterface<Collection<int, T>, array<int, T>>
 */
class DoctrineCollectionToArray implements ConverterInterface
{
    /**
     * @param Collection<int, T> $input
     * @return array<int, T>
     */
    public function convert(Collection $input): array
    {
        return $input->toArray();
    }
}
