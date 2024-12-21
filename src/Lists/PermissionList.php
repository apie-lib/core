<?php
namespace Apie\Core\Lists;

use Apie\Core\Permissions\PermissionInterface;

final class PermissionList extends ItemList
{
    public function offsetGet(mixed $offset): PermissionInterface|string
    {
        return parent::offsetGet($offset);
    }

    public function toStringList(): StringSet
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
        return new StringSet($res);
    }

    public function jsonSerialize(): array
    {
        return $this->toStringList()->toArray();
    }

    public function hasOverlap(PermissionList $permissionList): bool
    {
        $currentList = $this->toStringList()->toArray();
        $compareList = $permissionList->toStringList();
        if (empty($currentList)) {
            return (isset($compareList[''])) ;
        }
        $compareList = $compareList->toArray();
        if (empty($compareList)) {
            $compareList[] = '';
        }
        $currentList = array_combine($currentList, $currentList);
        foreach ($compareList as $item) {
            if (isset($currentList[$item])) {
                return true;
            }
        }
        return false;
    }
}
