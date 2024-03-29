<?php
namespace Apie\Core\Lists;

use Apie\Core\Permissions\PermissionInterface;

final class PermissionList extends ItemList
{
    public function offsetGet(mixed $offset): PermissionInterface|string
    {
        return parent::offsetGet($offset);
    }

    public function toStringList(): StringList
    {
        $res = [];
        foreach ($this as $value) {
            if ($value instanceof PermissionInterface) {
                foreach ($value->getPermissionIdentifiers()->toStringList() as $identifier) {
                    $res[] = $identifier;
                }
            } else {
                $res[] = (string) $value;
            }
        }
        return new StringList($res);
    }
}
