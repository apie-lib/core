<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Other\DiscriminatorMapping;
use Apie\Core\ReflectionTypeFactory;
use ReflectionClass;
use ReflectionType;

class DiscriminatorColumn implements FieldInterface, GetterInterface
{
    public function __construct(private DiscriminatorMapping $discriminatorMapping)
    {
    }

    public function getValue(object $object, ApieContext $apieContext): mixed
    {
        return $this->discriminatorMapping->getDiscriminatorForObject($object);
    }

    /**
     * @param ReflectionClass<PolymorphicEntityInterface> $class
     */
    public function getValueForClass(ReflectionClass $class): string
    {
        return $this->discriminatorMapping->getDiscriminatorForClass($class);
    }

    /** @return array<string, string> */
    public function getOptions(ApieContext $apieContext, bool $runtimeFilter = false): array
    {
        $options = [];
        foreach ($this->discriminatorMapping->getConfigs() as $config) {
            $refl = new ReflectionClass($config->getClassName());
            $constructor = $refl->getConstructor();
            if (!$runtimeFilter
                || $apieContext->appliesToContext($refl)
                || ($constructor === null || $apieContext->appliesToContext($constructor))) {
                $options[$config->getDiscriminator()] = $refl->name;
            }
        }
        return $options;
    }

    public function isRequired(): bool
    {
        return true;
    }

    public function isField(): bool
    {
        return true;
    }

    public function appliesToContext(ApieContext $apieContext): bool
    {
        return true;
    }

    public function getTypehint(): ?ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType('string');
    }

    public function getFieldPriority(): int
    {
        return -280;
    }
}
