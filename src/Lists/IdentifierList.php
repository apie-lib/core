<?php
namespace Apie\Core\Lists;

use Apie\Core\Identifiers\Identifier;

final class IdentifierList extends ItemList
{
    protected bool $mutable = false;
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!($value instanceof Identifier)) {
            $value = Identifier::fromNative($value);
        }
        parent::offsetSet($offset, $value);
    }

    public function offsetGet(mixed $offset): Identifier
    {
        return parent::offsetGet($offset);
    }

    /**
     * @return string[]
     */
    public function toStringArray(): array
    {
        $result = [];
        foreach ($this as $item) {
            $result[] = $item->toNative();
        }
        return $result;
    }
}
