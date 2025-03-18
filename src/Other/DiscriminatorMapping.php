<?php
namespace Apie\Core\Other;

use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Exceptions\DiscriminatorValueException;
use Apie\Core\Exceptions\InvalidTypeException;
use ReflectionClass;

#[SchemaMethod('provideSchema')]
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

    /**
     * @param ReflectionClass<PolymorphicEntityInterface>|PolymorphicEntityInterface $class
     */
    public function getConfigForClass(ReflectionClass|PolymorphicEntityInterface $class): DiscriminatorConfig
    {
        if ($class instanceof PolymorphicEntityInterface) {
            $class = new ReflectionClass($class);
        }
        $classes = [];
        foreach ($this->configs as $config) {
            $refl = new ReflectionClass($config->getClassName());
            if ($refl->name === $class->name || $class->isSubclassOf($refl)) {
                return $config;
            }
            $classes[] = $config->getClassName();
        }
        throw new InvalidTypeException(
            $class->name,
            $classes ? implode(', ', $classes) : 'none'
        );
    }

    /**
     * @param ReflectionClass<PolymorphicEntityInterface> $class
     */
    public function getDiscriminatorForClass(ReflectionClass $class): string
    {
        return $this->getConfigForClass($class)->getDiscriminator();
    }

    public function getDiscriminatorForObject(object $object): string
    {
        return $this->getConfigForClass($object)->getDiscriminator();
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

    /**
     * @return array<string, mixed>
     */
    public static function provideSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => true,
        ];
    }
}
