<?php
namespace Apie\Core\FileStorage;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadedFileAwareStorageInterface extends FileStorageInterface
{
    public function uploadedFileToPath(UploadedFile $uploadedFile): string;

    public function pathToUploadedFile(string $path): UploadedFile;
}
