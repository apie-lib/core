<?php
namespace Apie\Core\Other;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

final class UploadedFileFactory
{
    /** @test */
    private function __construct()
    {
    }

    public static function createUploadedFileFromString(string $contents, string $originalName, string $mimeType): UploadedFileInterface
    {
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
