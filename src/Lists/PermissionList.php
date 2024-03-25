<?php
namespace Apie\Core\Lists;

use Apie\Core\Permissions\PermissionInterface;
use Apie\Core\Lists\ItemList;

final class PermissionList extends ItemList
{
    public function offsetGet(mixed $offset): PermissionInterface|string
    {
        return parent::offsetGet($offset);
    }
}
