<?php
namespace Apie\Core\Persistence\Lists;

use Apie\Core\Lists\ItemList;
use Apie\Core\Persistence\PersistenceFieldInterface;

class PersistenceFieldList extends ItemList
{
    protected bool $mutable = false;
    public function offsetGet(mixed $key): PersistenceFieldInterface
    {
        return parent::offsetGet($key);
    }
}
