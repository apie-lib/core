<?php
namespace Apie\Core\Lists;

class StringHashmap extends ItemHashmap
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): string
    {
        return parent::offsetGet($offset);
    }
}
