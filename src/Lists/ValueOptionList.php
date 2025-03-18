<?php
namespace Apie\Core\Lists;

use Apie\Core\Dto\ValueOption;

class ValueOptionList extends ItemList
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): ValueOption
    {
        return parent::offsetGet($offset);
    }
}
