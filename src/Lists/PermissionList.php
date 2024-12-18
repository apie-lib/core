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

    public function jsonSerialize(): array
    {
        return $this->toStringList()->toArray();
    }

    public function hasOverlap(PermissionList $permissionList): bool
    {
        $currentList = $this->toStringList()->toArray();
        if (empty($currentList)) {
            $currentList = [''];
        }
        $currentList = array_combine($currentList, $currentList);
        foreach ($permissionList as $item) {
            if ($item instanceof PermissionInterface) {
                foreach ($item->getPermissionIdentifiers()->toStringList() as $identifier) {
                    if (isset($currentList[$identifier])) {
                        return true;
                    }
                }
            } elseif (isset($currentList[$item])) {
                return true;
            }
        }
        return false;
    }
}
