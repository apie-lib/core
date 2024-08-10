<?php
namespace Apie\Core\Lists;

use Psr\Http\Message\UploadedFileInterface;

class UploadedFileList extends ItemList
{
    public function offsetGet(mixed $offset): UploadedFileInterface
    {
        return parent::offsetGet($offset);
    }

    public static function createRandom(): self
    {
        return new UploadedFileList([]);
    }
}
