<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Attributes\FakeFile;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Faker\Interfaces\ApieImageFileFaker;

#[FakeFile(ApieImageFileFaker::class)]
final class ImageFile extends StoredFile
{
    protected function validateState(): void
    {
        $serverMime = $this->getServerMimeType();
        if (!str_starts_with($serverMime, 'image/')) {
            throw new InvalidTypeException($serverMime, 'image mime type');
        }
    }
}
