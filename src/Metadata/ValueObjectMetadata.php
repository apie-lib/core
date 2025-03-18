<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Dto\ValueOption;
use Apie\Core\Enums\ScalarType;
use Apie\Core\Lists\StringList;
use Apie\Core\Lists\ValueOptionList;
use Apie\Core\ValueObjects\Interfaces\LimitedOptionsInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use ReflectionClass;

class ValueObjectMetadata implements NullableMetadataInterface
{
    // TODO: check regex to see if the options are limited?
    public function getValueOptions(ApieContext $context, bool $runtimeFilter = false): ?ValueOptionList
    {
        if (in_array(LimitedOptionsInterface::class, $this->class->getInterfaceNames())) {
            //$translator = $context->getContext(ApieTranslator::class, false) ? : new ApieTranslator();
            $options = [];
            foreach ($this->class->getMethod('getOptions')->invoke(null) as $option) {
                $options[] = new ValueOption(Utils::toString($option), $option);
            }
            return new ValueOptionList($options);
        }
        return null;
    }
    /**
     * @param ReflectionClass<ValueObjectInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    /**
     * @return ReflectionClass<ValueObjectInterface>
     */
    public function toClass(): ReflectionClass
    {
        return $this->class;
    }

    public function getNativeType(): MetadataInterface
    {
        $method = $this->class->getMethod('toNative');
        return MetadataFactory::getCreationMetadata($method->getReturnType(), new ApieContext());
    }

    public function getHashmap(): MetadataFieldHashmap
    {
        return $this->getNativeType()->getHashmap();
    }

    public function getRequiredFields(): StringList
    {
        return $this->getNativeType()->getRequiredFields();
    }

    public function toScalarType(bool $ignoreNull = false): ScalarType
    {
        return $this->getNativeType()->toScalarType($ignoreNull);
    }

    public function getArrayItemType(): ?MetadataInterface
    {
        return $this->getNativeType()->getArrayItemType();
    }
}
