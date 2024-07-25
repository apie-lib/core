<?php
namespace Apie\Core\FileStorage;

use Psr\Http\Message\UploadedFileInterface;

/**
 * Store files by just returning a path containing mime type, original filename and contents
 */
class InlineStorage implements PsrAwareStorageInterface, ResourceAwareStorageInterface
{
    public function createNewUpload(
        UploadedFileInterface $fileUpload,
        string $className = StoredFile::class
    ): StoredFile {
        $storagePath = $this->psrToPath($fileUpload);
        return $this->loadFromStorage($storagePath, $className);
    }

    public function getProxy(
        string $storagePath,
        string $className = StoredFile::class
    ): StoredFile {
        return $className::createFromStorage(
            $this,
            $storagePath
        );
    }

    public function loadFromStorage(
        string $storagePath,
        string $className = StoredFile::class
    ): StoredFile {
        list($mimeType, $originalName, $contents) = explode('|', $storagePath, 3);
        return $className::createFromString(base64_decode($contents), $mimeType, $originalName)
            ->markBeingStored($this, $storagePath);
    }

    public function resourceToPath(mixed $resource): string
    {
        assert(is_resource($resource));
        fseek($resource, 0);
        return base64_encode(stream_get_contents($resource));
    }

    public function pathToResource(string $path): mixed
    {
        return fopen('data://text/plain,' . base64_decode($path), 'r');
    }

    public function psrToPath(UploadedFileInterface $uploadedFile): string
    {
        return sprintf(
            '%s|%s|%s',
            str_replace('|', '', $uploadedFile->getClientMediaType()),
            str_replace('|', '', $uploadedFile->getClientFilename()),
            base64_encode($uploadedFile->getStream()->__toString())
        );
    }

    public function pathToPsr(string $path): UploadedFileInterface
    {
        list($mimeType, $originalName, $contents) = explode('|', $path, 3);
        return StoredFile::createFromString(base64_decode($contents), $mimeType, $originalName);
    }
}
