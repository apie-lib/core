<?php
namespace Apie\Core\Persistence\Lists;

use Apie\Core\Lists\ItemList;
use Apie\Core\Persistence\PersistenceTableInterface;

class PersistenceTableList extends ItemList
{
    protected bool $mutable = false;

    public function offsetGet(mixed $key): PersistenceTableInterface
    {
        return parent::offsetGet($key);
    }
}
