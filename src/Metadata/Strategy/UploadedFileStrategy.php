<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\FileStorage\StoredFile;
use Apie\Core\Metadata\StoredFileMetadata;
use Apie\Core\Metadata\StrategyInterface;
use ReflectionClass;

final class UploadedFileStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        do {
            if ($class->name === StoredFile::class) {
                return true;
            }
        } while ($class = $class->getParentClass());
        return false;
    }

    /**
     * @param ReflectionClass<StoredFile> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): StoredFileMetadata
    {
        return new StoredFileMetadata($this->class, false, true);
    }

    public function getModificationMetadata(ApieContext $context): StoredFileMetadata
    {
        return new StoredFileMetadata($this->class, false, false);
    }

    public function getResultMetadata(ApieContext $context): StoredFileMetadata
    {
        return new StoredFileMetadata($this->class, true, false);
    }
}
