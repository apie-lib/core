<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\FileStorage\StoredFile;
use Apie\Core\Metadata\StoredFileMetadata;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Metadata\ValueObjectMetadata;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;

class FileUriStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        $interfaces = $class->getInterfaceNames();
        return (in_array(UploadedFileInterface::class, $interfaces, true) && in_array(ValueObjectInterface::class, $interfaces, true));
    }

    /**
     * @param ReflectionClass<StoredFile> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): ValueObjectMetadata
    {
        return new ValueObjectMetadata($this->class);
    }

    public function getModificationMetadata(ApieContext $context): ValueObjectMetadata
    {
        return $this->getCreationMetadata($context);
    }

    public function getResultMetadata(ApieContext $context): StoredFileMetadata
    {
        return new StoredFileMetadata($this->class, true, false);
    }
}
