<?php
namespace Apie\Core\Lists;

use Apie\Core\Context\ApieContext;
use ReflectionClass;

final class ReflectionClassList extends ItemList
{
    public function offsetGet(mixed $offset): ReflectionClass
    {
        return parent::offsetGet($offset);
    }

    public function filterOnApieContext(ApieContext $apieContext): self
    {
        $clone = new ReflectionClassList();
        $clone->internal = array_values(array_filter(
            $this->internal,
            function (ReflectionClass $item) use ($apieContext) {
                return $apieContext->appliesToContext($item);
            }
        ));
        return $clone;
    }
}
