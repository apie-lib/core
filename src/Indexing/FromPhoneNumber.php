<?php
namespace Apie\Core\Indexing;

use Apie\Core\Context\ApieContext;
use Apie\CountryAndPhoneNumber\PhoneNumber;

final class FromPhoneNumber implements IndexingStrategyInterface
{
    public function support(object $object): bool
    {
        return $object instanceof PhoneNumber;
    }

    /**
     * @return array<string, int>
     */
    public function getIndexes(object $class, ApieContext $context, Indexer $indexer): array
    {
        assert($class instanceof PhoneNumber);
        return [
            $class->toE164() => 3,
            str_replace(' ', '', $class->toInternational()) => 3,
            str_replace(' ', '', $class->toNational()) => 1,
            $class->toRFC3966() => 2,
        ];
    }
}
