<?php
namespace Apie\Core\Policies;

use Apie\Core\Lists\ItemHashmap;

class PolicyProviderHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): PolicyProviderInterface
    {
        return parent::offsetGet($offset);
    }
}
