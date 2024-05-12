<?php
namespace Apie\Core\Lists;

class StringSet extends ItemSet
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): string
    {
        return parent::offsetGet($offset);
    }
}
