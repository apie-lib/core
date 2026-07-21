<?php
namespace Apie\Core\Translator\Lists;

use Apie\Core\Lists\ItemSet;
use Apie\Core\Translator\ValueObjects\TranslationStringSuffix;

class TranslationStringSuffixSet extends ItemSet
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): TranslationStringSuffix
    {
        return parent::offsetGet($offset);
    }
}
