<?php
namespace Apie\Core\FileStorage;

use Psr\Http\Message\UploadedFileInterface;

interface PsrAwareStorageInterface extends FileStorageInterface
{
    public function psrToPath(UploadedFileInterface $uploadedFile): string;

    public function pathToPsr(string $path): UploadedFileInterface;
}
