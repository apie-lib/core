<?php
namespace Apie\Core\Lists;

use Apie\Core\Dto\MessageAndTimestamp;

class MessageAndTimestampList extends ItemList
{
    public function offsetGet(mixed $offset): MessageAndTimestamp
    {
        return parent::offsetGet($offset);
    }
}
