<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Exceptions\InvalidTypeException;

final class ImageFile extends StoredFile
{
    protected function validateState(): void
    {
        $serverMime = $this->getServerMimeType();
        if ($serverMime !== null && !str_starts_with($serverMime, 'image/')) {
            throw new InvalidTypeException($serverMime, 'image mime type');
        }
    }
}