<?php
namespace Apie\Core\Persistence\Lists;

use Apie\Core\Lists\ItemList;
use Apie\Core\Persistence\PersistenceTableFactoryInterface;

class PersistenceTableFactoryList extends ItemList
{
    protected bool $mutable = false;
    public function offsetGet(mixed $key): PersistenceTableFactoryInterface
    {
        return parent::offsetGet($key);
    }
}
