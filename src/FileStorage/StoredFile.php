<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Enums\UploadedFileStatus;
use Apie\CountWords\WordCounter;
use finfo;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Symfony\Component\Mime\MimeTypes;

class StoredFile implements UploadedFileInterface
{
    private ?string $movedPath = null;
    /**
     * @param resource|null $resource
     * @param array<string, int>|null $indexing
     */
    final protected function __construct(
        private UploadedFileStatus $status,
        private ?FileStorageInterface $storage = null,
        private ?string $content = null,
        private ?string $storagePath = null,
        private mixed $resource = null,
        private ?string $clientMimeType = null,
        private ?string $clientOriginalFile = null,
        private ?int $fileSize = null,
        private ?string $serverMimeType = null,
        private ?string $serverPath = null,
        private ?UploadedFileInterface $internalFile = null,
        private ?array $indexing = null,
        private bool $removeOnDestruct = false,
    ) {
        $this->validateState();
    }

    protected function validateState(): void
    {
    }

    public function withBeingStored(FileStorageInterface $storage, string $storagePath): static
    {
        $res = clone $this;
        $res->status = UploadedFileStatus::StoredInStorage;
        $res->storage = $storage;
        $res->storagePath = $storagePath;
        return $res;
    }

    final public function __destruct()
    {
        if ($this->removeOnDestruct && $this->serverPath && file_exists($this->serverPath)) {
            unlink($this->serverPath);
        }
    }

    public final static function createFromStorage(FileStorageInterface $storage, string $storagePath): static
    {
        return new static(
            UploadedFileStatus::StoredInStorage,
            storage: $storage,
            storagePath: $storagePath
        );
    }

    public final static function createFromString(
        string $content,
        ?string $clientMimeType = null,
        ?string $clientOriginalFile = null
    ): static {
        return new static(
            UploadedFileStatus::CreatedLocally,
            content: $content,
            clientMimeType: $clientMimeType,
            clientOriginalFile: $clientOriginalFile
        );
    }

    public final static function createFromLocalFile(string $serverPath, ?string $clientMimeType = null, bool $removeOnDestruct = false): static
    {
        return new static(
            UploadedFileStatus::CreatedLocally,
            clientMimeType: $clientMimeType,
            serverPath: $serverPath,
            removeOnDestruct: $removeOnDestruct
        );
    }

    /**
     * @param resource $resource
     */
    public final static function createFromResource(
        mixed $resource,
        ?string $clientMimeType = null,
        ?string $clientOriginalFile = null,
    ): static
    {
        assert(is_resource($resource));
        assert('stream' === get_resource_type($resource));
        return new static(
            UploadedFileStatus::FromRequest,
            resource: $resource,
            clientMimeType: $clientMimeType,
            clientOriginalFile: $clientOriginalFile
        );
    }

    public final static function createFromUploadedFile(UploadedFileInterface $uploadedFile, ?string $storagePath = null): static
    {
        if (get_class($uploadedFile) === static::class) {
            return $uploadedFile;
        }
        if ($uploadedFile instanceof StoredFile) {
            return new static(
                status: $uploadedFile->status,
                storage: $uploadedFile->storage,
                content: $uploadedFile->content,
                storagePath: $storagePath ?? $uploadedFile->storagePath,
                resource: $uploadedFile->resource,
                clientMimeType: $uploadedFile->clientMimeType,
                clientOriginalFile: $uploadedFile->clientOriginalFile,
                fileSize: $uploadedFile->fileSize,
                serverMimeType:$uploadedFile->serverMimeType,
                serverPath: $uploadedFile->serverPath,
                internalFile: $uploadedFile,
                indexing: $uploadedFile->indexing,
                removeOnDestruct: false
            );
        }
        return new static(
            UploadedFileStatus::FromRequest,
            storagePath: $storagePath,
            internalFile: $uploadedFile
        );
    }

    public final function getStatus(): UploadedFileStatus
    {
        return $this->status;
    }

    public final function getContent(): string
    {
        if ($this->content !== null) {
            return $this->content;
        }
        if ($this->serverPath && file_exists($this->serverPath)) {
            $this->content = file_get_contents($this->serverPath);
            return $this->content;
        }
        if (is_resource($this->resource)) {
            $this->content = stream_get_contents($this->resource);
            return $this->content;
        }
        if ($this->storage instanceof ChainedFileStorage) {
            $this->internalFile = $this->storage->pathToPsr($this->storagePath);
        }
        if ($this->internalFile !== null) {
            if ($this->internalFile instanceof StoredFile) {
                return $this->content = $this->internalFile->getContent();
            }
            return $this->content = $this->internalFile->getStream()->__toString();
        }

        throw new \LogicException('Could not load content');
    }

    /**
     * @return array<string, int>
     */
    public function getIndexing(): array
    {
        if (null !== $this->indexing) {
            return $this->indexing;
        }
        if ($this->internalFile instanceof StoredFile) {
            return $this->indexing = $this->internalFile->getIndexing();
        }
        if ($this->serverPath && file_exists($this->serverPath)) {
            return $this->indexing = WordCounter::countFromFile($this->serverPath);
        }
        if (is_resource($this->resource)) {
            return $this->indexing = WordCounter::countFromResource($this->resource);
        }
        return [];
    }

    public function getStoragePath(): ?string
    {
        return $this->storagePath;
    }

    /**
     * @param resource $resource
     * @return resource
     */
    private function makeRewindable(mixed $resource): mixed
    {
        $tempStream = tmpfile();
        if ($tempStream === false) {
            throw new RuntimeException('Unable to create a temporary file');
        }
        stream_copy_to_stream($resource, $tempStream);
        rewind($tempStream);

        return $tempStream;
    }

    public final function getStream(): StreamInterface
    {
        if ($this->content !== null) {
            return Stream::create($this->content);
        }
        if ($this->serverPath) {
            return new Stream(fopen($this->serverPath, 'r'));
        }
        if (is_resource($this->resource)) {
            $meta = stream_get_meta_data($this->resource);
            if (!is_writable($meta['uri']) || !is_readable($meta['uri']) || 'a' === $meta['mode']) {
                $this->resource = $this->makeRewindable($this->resource);
            }
            return new Stream($this->makeRewindable($this->resource));
        }
        if ($this->storage instanceof ChainedFileStorage) {
            $this->internalFile = $this->storage->pathToPsr($this->storagePath);
        }
        if (null !== $this->internalFile) {
            return $this->internalFile->getStream();
        }
        throw new \LogicException("I have no idea how to make a stream for this uploaded file");
    }
    public function moveTo(string $targetPath): void
    {
        if ($this->movedPath !== null) {
            throw new \LogicException('File is already moved to ' . $this->movedPath);
        }
        if ($this->serverPath !== null && !$this->removeOnDestruct) {
            throw new \LogicException($this->serverPath . ' is not a temporary file');
        }
        $this->movedPath = $targetPath;
        if ($this->storage instanceof ChainedFileStorage) {
            $this->internalFile = $this->storage->pathToPsr($this->storagePath);
        }
        if ($this->internalFile) {
            $this->internalFile->moveTo($targetPath);
            return;
        }
        if ($this->content !== null) {
            file_put_contents($targetPath, $this->content);
        }
        if ($this->serverPath !== null) {
            move_uploaded_file($this->serverPath, $targetPath);
        }
    }
    public final function getSize(): ?int
    {
        if ($this->fileSize !== null) {
            return $this->fileSize;
        }
        if ($this->content !== null) {
            return strlen($this->content);
        }
        if ($this->serverPath) {
            $size = filesize($this->serverPath);
            // size < 0 is possible on 32bit systems with files larger than 2GB.
            if ($size === false || $size < 0) {
                return null;
            }
            return $size;
        }

        return null;
    }
    public final function getError(): int
    {
        if (null !== $this->internalFile) {
            return $this->internalFile->getError();
        }
        if ($this->serverPath) {
            return file_exists($this->serverPath) ? UPLOAD_ERR_OK : UPLOAD_ERR_NO_FILE;
        }
        return UPLOAD_ERR_OK;
    }
    public final function getClientFilename(): ?string
    {
        if (null !== $this->internalFile) {
            return $this->internalFile->getClientFilename();
        }
        if ($this->clientOriginalFile !== null) {
            return $this->clientOriginalFile;
        }
        if ($this->serverPath !== null) {
            return $this->clientOriginalFile = basename($this->serverPath);
        }
        return null;
    }
    public final function getClientMediaType(): ?string
    {
        if (null !== $this->internalFile) {
            return $this->internalFile->getClientMediaType();
        }
        return $this->clientMimeType;
    }

    public final function getServerMimeType(): string
    {
        if (!$this->serverMimeType) {
            if ($this->serverPath && file_exists($this->serverPath)) {
                return $this->serverMimeType = MimeTypes::getDefault()->guessMimeType($this->serverPath);
            }
            if ($this->content) {
                $finfo = new finfo();
                return $this->serverMimeType = $finfo->buffer($this->content, FILEINFO_MIME_TYPE);
            }
            if (null !== $this->internalFile) {
                $content = $this->getContent();
                $finfo = new finfo();
                return $this->serverMimeType = $finfo->buffer($content, FILEINFO_MIME_TYPE);
            }
        }

        return $this->serverMimeType;
    }

    public final function getServerPath(): ?string
    {
        return $this->serverPath;
    }
}