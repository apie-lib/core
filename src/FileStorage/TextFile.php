<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Attributes\FakeFile;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Faker\Interfaces\ApieImageFileFaker;

#[FakeFile(ApieImageFileFaker::class)]
final class TextFile extends StoredFile
{
    protected function validateState(): void
    {
        $serverMime = $this->getServerMimeType();
        if (!preg_match('/plain|text/i', $serverMime)) {
            throw new InvalidTypeException($serverMime, 'text mime type');
        }
    }
}
