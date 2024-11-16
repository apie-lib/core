<?php
namespace Apie\Core\Lists;

use ReflectionType;

class ReflectionTypeSet extends ItemSet
{
    public function offsetGet(mixed $offset): ReflectionType
    {
        return parent::offsetGet($offset);
    }
}
