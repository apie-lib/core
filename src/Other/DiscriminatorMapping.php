<?php
namespace Apie\Core\Other;

use Apie\Core\Exceptions\DiscriminatorValueException;

final class DiscriminatorMapping
{
    /** @DiscriminatorConfig[] */
    private array $configs;

    public function __construct(DiscriminatorConfig... $configs)
    {
        $this->configs = $configs;
    }

    /**
     * @return array
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
