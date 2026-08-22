<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\Lists\ValueOptionList;
use Apie\TypeConverter\ReflectionTypeFactory;
use BcMath\Number;
use GMP;
use ReflectionClass;
use Uri\Rfc3986\Uri;

class CustomObjectsMetadata implements MetadataInterface
{
    /**
     * @var array<class-string<covariant object>, string> $mapping
     */
    private static array $mapping = [
        Uri::class => 'string',
        Number::class => 'string',
        GMP::class => 'string',
    ];

    private MetadataInterface $internal;

    /**
     * @param ReflectionClass<covariant object> $class
     */
    public function __construct(private readonly ReflectionClass $class)
    {
        $this->internal = new ScalarMetadata(ScalarType::MIXED);
        if (isset(self::$mapping[$class->name])) {
            $strategy = MetadataFactory::getMetadataStrategyForType(
                ReflectionTypeFactory::createReflectionType(self::$mapping[$class->name])
            );
            $this->internal = $strategy->getResultMetadata(new ApieContext());
        }
    }
    public function getDisplayName(): string
    {
        return $this->class->getShortName();
    }
    public function getHashmap(): MetadataFieldHashmap
    {
        return new MetadataFieldHashmap();
    }
    public function getRequiredFields(): StringList
    {
        return new StringList();
    }
    public function toScalarType(): ScalarType
    {
        return $this->internal->toScalarType();
    }
    public function getArrayItemType(): ?MetadataInterface
    {
        return null;
    }
    public function getValueOptions(ApieContext $context, bool $runtimeFilter = false): ?ValueOptionList
    {
        return $this->internal->getValueOptions($context, $runtimeFilter);
    }
    public function toClass(): ?ReflectionClass
    {
        return $this->class;
    }
}
