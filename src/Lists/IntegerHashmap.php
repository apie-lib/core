<?php

namespace Apie\Core\Lists;

class IntegerHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): int
    {
        return parent::offsetGet($offset);
    }
}
