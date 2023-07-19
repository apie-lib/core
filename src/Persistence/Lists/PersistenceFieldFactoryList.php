<?php
namespace Apie\Core\Persistence\Lists;

use Apie\Core\Lists\ItemList;
use Apie\Core\Persistence\PersistenceFieldFactoryInterface;

class PersistenceFieldFactoryList extends ItemList
{
    protected bool $mutable = false;
    public function offsetGet(mixed $key): PersistenceFieldFactoryInterface
    {
        return parent::offsetGet($key);
    }
}
