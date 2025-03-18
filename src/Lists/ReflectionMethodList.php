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

    public function filterOnApieContext(ApieContext $apieContext, bool $runtimeChecks = true): self
    {
        $clone = new ReflectionMethodList();
        $clone->internal = array_values(array_filter(
            $this->internal,
            function (ReflectionMethod $item) use ($apieContext, $runtimeChecks) {
                return $apieContext->appliesToContext($item, $runtimeChecks);
            }
        ));
        return $clone;
    }
}
