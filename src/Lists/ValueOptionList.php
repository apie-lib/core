<?php
namespace Apie\Core\Lists;

use Apie\Core\Dto\ValueOption;

final class ValueOptionList extends ItemList
{
    protected bool $mutable = false;

    private bool $integerKeys;

    public function hasIntegerKeys(): bool
    {
        if (isset($this->integerKeys)) {
            return $this->integerKeys;
        }
        foreach ($this->internal as $value) {
            if (preg_match('/^\d+$/', $value->name)) {
                $this->integerKeys = true;
                return true;
            }
        }
        $this->integerKeys = false;
        return false;
    }

    public function offsetGet(mixed $offset): ValueOption
    {
        return parent::offsetGet($offset);
    }


}
