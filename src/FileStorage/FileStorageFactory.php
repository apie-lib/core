<?php
namespace Apie\Core\FileStorage;

final class FileStorageFactory
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param array<string|int, mixed> $options
     */
    public static function create(?array $options = null): ChainedFileStorage
    {
        $options ??= [['class' => InlineStorage::class]];
        $psrAwareStorages = [];
        $resourceAwareStorages = [];
        foreach ($options as $option) {
            $instance = new ($option['class'])($option['options'] ?? null);
            if ($instance instanceof PsrAwareStorageInterface) {
                $psrAwareStorages[] = $instance;
            }
            if ($instance instanceof ResourceAwareStorageInterface) {
                $resourceAwareStorages[] = $instance;
            }
        }
        return new ChainedFileStorage($psrAwareStorages, $resourceAwareStorages);
    }
}
