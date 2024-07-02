<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Other\UploadedFileFactory;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Store files by just returning a path containing mime type, original filename and contents
 */
class InlineStorage implements PsrAwareStorageInterface, UploadedFileAwareStorageInterface, ResourceAwareStorageInterface
{
    public function uploadedFileToPath(UploadedFile $uploadedFile): string
    {
        return sprintf(
            '%s|%s|%s',
            str_replace('|', '', $uploadedFile->getMimeType()),
            str_replace('|', '', $uploadedFile->getClientOriginalName()),
            $uploadedFile->getContent()
        );
    }

    public function pathToUploadedFile(string $path): UploadedFile
    {
        list($mimeType, $originalName, $contents) = explode('|', $path, 3);
        $tmpFilePath = sys_get_temp_dir() . '/upload-' . md5($path);
        file_put_contents($tmpFilePath, $contents);

        return new UploadedFile(
            $tmpFilePath,
            $originalName,
            $mimeType,
            UPLOAD_ERR_OK,
            true
        );
    }

    public function resourceToPath(mixed $resource): string
    {
        assert(is_resource($resource));
        fseek($resource, 0);
        return stream_get_contents($resource);
    }

    public function pathToResource(string $path): mixed
    {
        return fopen('data://text/plain,' . $path, 'r');
    }

    public function psrToPath(UploadedFileInterface $uploadedFile): string
    {
        return sprintf(
            '%s|%s|%s',
            str_replace('|', '', $uploadedFile->getClientMediaType()),
            str_replace('|', '', $uploadedFile->getClientFilename()),
            $uploadedFile->getStream()->__toString()
        );
    }

    public function pathToPsr(string $path): UploadedFileInterface
    {
        list($mimeType, $originalName, $contents) = explode('|', $path, 3);
        return UploadedFileFactory::createUploadedFileFromString($contents, $originalName, $mimeType);
    }
}
