<?php
namespace Apie\Core\Lists;

use Apie\Core\Identifiers\Identifier;

final class IdentifierList extends ItemList
{
    protected bool $mutable = false;
    
    public function offsetGet(mixed $offset): Identifier
    {
        return parent::offsetGet($offset);
    }
}
