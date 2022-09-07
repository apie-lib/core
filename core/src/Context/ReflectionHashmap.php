<?php
namespace Apie\Core\Context;

use Apie\Core\Lists\ItemHashmap;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Contains a list of methods and/or properties.
 */
final class ReflectionHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): ReflectionMethod|ReflectionProperty
    {
        return parent::offsetGet($offset);
    }
}
