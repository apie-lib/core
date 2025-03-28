<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Exceptions\FileStorageException;
use Exception;
use LogicException;
use Psr\Http\Message\UploadedFileInterface;
use Throwable;

final class ChainedFileStorage implements PsrAwareStorageInterface, ResourceAwareStorageInterface
{
    /**
     * @var array<int, PsrAwareStorageInterface> $psrAwareStorages
     */
    private array $psrAwareStorages;

    /**
     * @var array<int, ResourceAwareStorageInterface> $resourceAwareStorages
     */
    private array $resourceAwareStorages;

    /**
     * @param array<int, PsrAwareStorageInterface> $psrAwareStorages
     * @param array<int, ResourceAwareStorageInterface> $resourceAwareStorages
     */
    public function __construct(
        iterable $psrAwareStorages,
        iterable $resourceAwareStorages
    ) {
        $this->psrAwareStorages = is_array($psrAwareStorages) ? $psrAwareStorages : iterator_to_array($psrAwareStorages);
        $this->resourceAwareStorages = is_array($resourceAwareStorages) ? $resourceAwareStorages : iterator_to_array($resourceAwareStorages);
    }

    public function createNewUpload(
        UploadedFileInterface $fileUpload,
        string $className = StoredFile::class
    ): StoredFile {
        foreach ($this->psrAwareStorages as $psrAwareStorage) {
            return $psrAwareStorage->createNewUpload($fileUpload, $className);
        }
        foreach ($this->resourceAwareStorages as $resourceAwareStorage) {
            return $resourceAwareStorage->createNewUpload($fileUpload, $className);
        }

        throw new \LogicException("I can not create an upload");
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

    /**
     * @template T of StoredFile
     * @param class-string<T> $className
     * @return T
     */
    public function loadFromStorage(
        string $storagePath,
        string $className = StoredFile::class
    ): StoredFile {
        $errors = [];
        $list = [...$this->psrAwareStorages, ...$this->resourceAwareStorages];
        foreach ($list as $psrAwareStorage) {
            try {
                return $psrAwareStorage->loadFromStorage($storagePath, $className);
            } catch (Exception $error) {
                $errors[] = $error;
            }
        }

        throw new FileStorageException('I can not load from "' . $storagePath . '"', $errors);
    }

    /**
     * @param array<int, object> $list
     */
    private function iterate(array $list, string $methodName, mixed... $arguments): mixed
    {
        $collectedExceptions = [];
        foreach ($list as $storage) {
            try {
                return $storage->$methodName(...$arguments);
            } catch (Throwable $error) {
                $collectedExceptions[] = $error;
            }
        }
        if (empty($collectedExceptions)) {
            $collectedExceptions[] = new LogicException('There is no configured storage class for ' . $methodName);
        }
        throw new FileStorageException('There was a problem calling ' . $methodName, $collectedExceptions);
    }

    public function pathToPsr(string $path): UploadedFileInterface
    {
        return $this->iterate($this->psrAwareStorages, 'pathToPsr', $path);
    }

    public function psrToPath(UploadedFileInterface $uploadedFile): string
    {
        return $this->iterate($this->psrAwareStorages, 'psrToPath', $uploadedFile);
    }

    public function resourceToPath(mixed $resource): string
    {
        return $this->iterate($this->resourceAwareStorages, 'resourceToPath', $resource);
    }

    public function pathToResource(string $path): mixed
    {
        return $this->iterate($this->resourceAwareStorages, 'pathToResource', $path);
    }
}
