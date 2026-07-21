<?php
namespace Apie\Core\Translator\Lists;

use Apie\Core\Lists\ItemSet;
use Apie\Core\Translator\ValueObjects\TranslationStringPrefix;

class TranslationStringPrefixSet extends ItemSet
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): TranslationStringPrefix
    {
        return parent::offsetGet($offset);
    }
}
