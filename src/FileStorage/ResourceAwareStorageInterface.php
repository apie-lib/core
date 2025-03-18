<?php
namespace Apie\Core\FileStorage;

interface ResourceAwareStorageInterface extends FileStorageInterface
{
    /**
     * @param resource $resource
     */
    public function resourceToPath(mixed $resource): string;

    /**
     * @return resource
     */
    public function pathToResource(string $path): mixed;
}
