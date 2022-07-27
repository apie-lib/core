<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\Lists\ItemHashmap;

class BoundedContextHashmap extends ItemHashmap
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): BoundedContext
    {
        return parent::offsetGet($offset);
    }
}
