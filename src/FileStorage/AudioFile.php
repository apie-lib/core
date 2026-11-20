<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Attributes\FakeFile;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Faker\Interfaces\ApieAudioFileFaker;

#[FakeFile(ApieAudioFileFaker::class)]
final class AudioFile extends StoredFile
{
    protected function validateState(): void
    {
        $serverMime = $this->getServerMimeType();
        if (!str_starts_with($serverMime, 'audio/')) {
            throw new InvalidTypeException($serverMime, 'audio mime type');
        }
    }
}
