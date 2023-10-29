<?php
namespace Apie\Core\Utils;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Other\DiscriminatorMapping;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionType;

final class EntityUtils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isEntity(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class->implementsInterface(EntityInterface::class)
            && !$class->isInterface();
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isNonPolymorphicEntity(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return !$class->implementsInterface(PolymorphicEntityInterface::class)
            && $class->implementsInterface(EntityInterface::class)
            && !$class->isInterface();
    }

    /**
     * @param string|ReflectionClass<object>|ReflectionProperty|ReflectionType|ReflectionMethod $input
     */
    public static function isPolymorphicEntity(string|ReflectionClass|ReflectionProperty|ReflectionType|ReflectionMethod $input): bool
    {
        $class = ConverterUtils::toReflectionClass($input);
        return $class->implementsInterface(PolymorphicEntityInterface::class)
            && !$class->isInterface();
    }

    /**
     * @param ReflectionClass<PolymorphicEntityInterface>|null $base
     * @return array<string, string>
     */
    public static function getDiscriminatorValues(PolymorphicEntityInterface $entity, ?ReflectionClass $base = null): array
    {
        if (!$base) {
            $refl = new ReflectionClass($entity);
            while ($refl) {
                if ($refl->getMethod('getDiscriminatorMapping')->getDeclaringClass()->name === $refl->name) {
                    $base = $refl;
                }
                $refl = $refl->getParentClass();
            }
        }
        assert($base !== null);
        $entityClass = get_class($entity);
        $result = [];
        $current = $base;
        $last = null;
        while($current->getMethod('getDiscriminatorMapping')->getDeclaringClass()->name !== $last && $current->name !== $entityClass) {
            /** @var DiscriminatorMapping $mapping */
            $mapping = $current->getMethod('getDiscriminatorMapping')->invoke(null);
            $config = $mapping->getConfigForClass($entity);
            $result[$mapping->getPropertyName()] = $config->getDiscriminator();
            $current = new ReflectionClass($config->getClassName());
            $last = $current->getMethod('getDiscriminatorMapping')->getDeclaringClass()->name;
        }
        return $result;
    }
}
