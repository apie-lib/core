<?php
namespace Apie\Core\FileStorage;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Store files by just returning a path containing mime type, original filename and contents
 */
class InlineStorage implements UploadedFileAwareStorageInterface, ResourceAwareStorageInterface
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
        return new class($contents, $originalName, $mimeType) implements UploadedFileInterface {
            public function __construct(
                private string $contents,
                private string $originalName,
                private string $mimeType
            ) {
            }
            public function getStream(): StreamInterface
            {
                $factory = new Psr17Factory();
                return $factory->createStream($this->contents);
            }
            public function moveTo(string $targetPath): void
            {
                throw new \RuntimeException('Not implemented');
            }
    
            public function getSize(): int
            {
                return strlen($this->contents);
            }
    
            public function getError(): int
            {
                return UPLOAD_ERR_OK;
            }
    
            public function getClientFilename(): string
            {
                return $this->originalName;
            }
            
            public function getClientMediaType(): string
            {
                return $this->mimeType;
            }

        };
    }
}
