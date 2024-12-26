<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\FileStorage\StoredFile;
use Apie\Core\Lists\StringList;
use Apie\Core\Lists\ValueOptionList;
use Apie\Core\Metadata\Fields\ConstructorParameter;
use Apie\Core\Metadata\Fields\GetterMethod;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

final class StoredFileMetadata implements MetadataInterface
{
    /**
     * @param ReflectionClass<StoredFile> $class
     */
    public function __construct(
        private readonly ReflectionClass $class,
        private readonly bool $getters,
        private readonly bool $setters
    ) {
    }

    public function getValueOptions(ApieContext $context, bool $runtimeFilter = false): ?ValueOptionList
    {
        return null;
    }

    /**
     * @return ReflectionClass<StoredFile>
     */
    public function toClass(): ReflectionClass
    {
        return $this->class;
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        $mapping = [];
        if ($this->getters) {
            $mapping['indexing'] = new GetterMethod(new ReflectionMethod(StoredFile::class, 'getIndexing'));
        }
        if ($this->setters) {
            $mapping['contents']  = new ConstructorParameter((new ReflectionParameter([StoredFile::class, '__construct'], 'content')));
        }
        return new MetadataFieldHashmap($mapping);
    }

    public function getRequiredFields(): StringList
    {
        return new StringList([]);
    }

    public function toScalarType(): ScalarType
    {
        return ScalarType::STDCLASS;
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        return null;
    }
}
