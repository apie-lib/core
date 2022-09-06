<?php
namespace Apie\Core\Other;

use Apie\Core\Exceptions\DiscriminatorValueException;
use Apie\Core\Exceptions\InvalidTypeException;
use ReflectionClass;

final class DiscriminatorMapping
{
    /** @var DiscriminatorConfig[] */
    private array $configs;

    public function __construct(private string $propertyName, DiscriminatorConfig... $configs)
    {
        $this->configs = $configs;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @return DiscriminatorConfig[]
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    public function getDiscriminatorForObject(object $object): string
    {
        $classes = [];
        foreach ($this->configs as $config) {
            $refl = new ReflectionClass($config->getClassName());
            if ($refl->isInstance($object)) {
                return $config->getDiscriminator();
            }
            $classes[] = $config->getClassName();
        }
        throw new InvalidTypeException(
            $object,
            $classes ? implode(', ', $classes) : 'none'
        );
    }

    public function getClassNameFromDiscriminator(string $discriminatorValue): string
    {
        foreach ($this->configs as $config) {
            if ($config->getDiscriminator() === $discriminatorValue) {
                return $config->getClassName();
            }
        }
        throw new DiscriminatorValueException($discriminatorValue);
    }
}
