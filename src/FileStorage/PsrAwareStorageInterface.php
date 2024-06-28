<?php
namespace Apie\Core\FileStorage;

use Psr\Http\Message\UploadedFileInterface;

interface PsrAwareStorageInterface
{
    public function psrToPath(UploadedFileInterface $uploadedFile): string;

    public function pathToPsr(string $path): UploadedFileInterface;
}
