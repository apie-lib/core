<?php
namespace Apie\Core\Context;

use Apie\Core\Lists\ItemHashmap;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;

/**
 * Contains a list of methods and/or properties.
 */
final class ReflectionHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): ReflectionType|ReflectionMethod|ReflectionProperty|ReflectionParameter
    {
        return parent::offsetGet($offset);
    }
}
