<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Optional;
use Apie\Core\FileStorage\StoredFile;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;

final class JsonFileUpload implements ValueObjectInterface
{
    use CompositeValueObject;
    public function __construct(
        private Filename $originalFilename,
        private BinaryStream $contents,
        #[Optional]
        private ?StrictMimeType $mime = null
    ) {
    }

    public function toUploadedFile(): StoredFile
    {
        return StoredFile::createFromString(
            $this->contents->toNative(),
            $this->mime?->toNative(),
            $this->originalFilename->toNative(),
        );
    }
}
