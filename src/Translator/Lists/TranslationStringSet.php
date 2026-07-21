<?php
namespace Apie\Core\Translator\Lists;

use Apie\Core\Lists\ItemSet;
use Apie\Core\Translator\ValueObjects\AbstractTranslation;
use Apie\Core\Translator\ValueObjects\TranslationString;

final class TranslationStringSet extends ItemSet
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): TranslationString|AbstractTranslation
    {
        return parent::offsetGet($offset);
    }

    public function toArray(): array
    {
        $list = parent::toArray();

        usort(
            $list,
            function (TranslationString|AbstractTranslation $a, TranslationString|AbstractTranslation $b) {
                return $a->getSpecifity() <=> $b->getSpecifity();
            }
        );

        return $list;
    }
}
