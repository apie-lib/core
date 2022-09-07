<?php
namespace Apie\Core\Lists;

class StringList extends ItemList
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): string
    {
        return parent::offsetGet($offset);
    }
}
