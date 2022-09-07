<?php
namespace Apie\Core\Actions;

use Apie\Core\Lists\ItemList;

class ActionResponseStatusList extends ItemList
{
    public function offsetGet(mixed $offset): ActionResponseStatus
    {
        return parent::offsetGet($offset);
    }
}
