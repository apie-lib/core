<?php
namespace Apie\Core\ValueObjects\Concerns;

use Apie\Core\Attributes\ProvideIndex;
use Apie\Core\ValueObjects\IsStringValueObject;
use Apie\CountWords\WordCounter;

/**
 * Put this attribute on your string value object and we will count the words in the string.
 * #[ProvideIndex('getIndexes')]
 * @see IsStringValueObject
 * @see ProvideIndex
 */
trait IndexesWords
{
    /**
     * @return array<string, int>
     */
    public function getIndexes(): array
    {
        return WordCounter::countFromString($this->internal);
    }
}
