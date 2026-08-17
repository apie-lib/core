<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\Concerns\NoValueOptions;
use ReflectionClass;

final class ItemHashmapMetadata implements MetadataInterface
{
    use NoValueOptions;

    /**
     * @param ReflectionClass<covariant ItemHashmap> $class
     */
    public function __construct(private readonly ReflectionClass $class, private readonly bool $creation = true)
    {
    }

    public function getDisplayName(): string
    {
        return $this->class->getShortName();
    }

    /**
     * @return ReflectionClass<covariant ItemHashmap>
     */
    public function toClass(): ReflectionClass
    {
        return $this->class;
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        return new MetadataFieldHashmap();
    }

    public function getRequiredFields(): StringList
    {
        return new StringList([]);
    }
    public function toScalarType(): ScalarType
    {
        return ScalarType::STDCLASS;
    }
    public function getArrayItemType(): MetadataInterface
    {
        $strategy = MetadataFactory::getMetadataStrategyForType(
            $this->class->getMethod('offsetGet')->getReturnType()
        );
        return $this->creation
            ? $strategy->getCreationMetadata(new ApieContext())
            : $strategy->getResultMetadata(new ApieContext());
    }
}
