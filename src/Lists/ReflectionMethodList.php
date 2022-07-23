<?php
namespace Apie\Core\Lists;

use Apie\Core\Context\ApieContext;
use ReflectionMethod;

final class ReflectionMethodList extends ItemList
{
    public function offsetGet(mixed $offset): ReflectionMethod
    {
        return parent::offsetGet($offset);
    }

    public function filterOnApieContext(ApieContext $apieContext): self
    {
        $clone = new ReflectionMethodList();
        $clone->internal = array_values(array_filter(
            $this->internal,
            function (ReflectionMethod $item) use ($apieContext) {
                return $apieContext->appliesToContext($item);
            }
        ));
        return $clone;
    }
}
