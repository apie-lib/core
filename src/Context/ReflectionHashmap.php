<?php
namespace Apie\Core\Context;

use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Other\DiscriminatorMapping;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;

/**
 * Contains a list of methods and/or properties.
 */
final class ReflectionHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): ReflectionMethod|ReflectionProperty|ReflectionParameter|DiscriminatorMapping
    {
        return parent::offsetGet($offset);
    }
}
