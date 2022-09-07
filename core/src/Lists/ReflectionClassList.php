<?php
namespace Apie\Core\Lists;

use Apie\Core\Context\ApieContext;
use ReflectionClass;

/**
 * @extends ItemList<ReflectionClass<object>>
 */
final class ReflectionClassList extends ItemList
{
    /**
     * @return ReflectionClass<object>
     */
    public function offsetGet(mixed $offset): ReflectionClass
    {
        return parent::offsetGet($offset);
    }

    /**
     * @return string[]
     */
    public function toStringArray(): array
    {
        return array_map(
            function (ReflectionClass $refl) {
                return $refl->getName();
            },
            $this->internal
        );
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
