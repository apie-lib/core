<?php
namespace Apie\Core\Other;

use Apie\Core\Exceptions\DiscriminatorValueException;

final class DiscriminatorMapping
{
    /** @DiscriminatorConfig[] */
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
