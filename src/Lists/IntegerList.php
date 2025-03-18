<?php

namespace Apie\Core\Lists;

class IntegerList extends ItemList
{
    public function offsetGet(mixed $offset): int
    {
        return parent::offsetGet($offset);
    }
}
