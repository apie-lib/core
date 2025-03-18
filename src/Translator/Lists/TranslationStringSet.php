<?php
namespace Apie\Core\Translator\Lists;

use Apie\Core\Lists\ItemSet;
use Apie\Core\Translator\ValueObjects\TranslationString;

final class TranslationStringSet extends ItemSet
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): TranslationString
    {
        return parent::offsetGet($offset);
    }
}
