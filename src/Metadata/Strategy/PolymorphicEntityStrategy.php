<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\MetadataFieldHashmap;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\Fields\DiscriminatorColumn;
use Apie\Core\Metadata\Fields\OptionalField;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\Other\DiscriminatorConfig;
use Apie\Core\Other\DiscriminatorMapping;
use ReflectionClass;

final class PolymorphicEntityStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(PolymorphicEntityInterface::class)
            && !$class->isInterface();
    }

    /**
     * @param ReflectionClass<PolymorphicEntityInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getModificationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        
        $class = $this->class;

        while ($class) {
            $method = $class->getMethod('getDiscriminatorMapping');
            if (!$method->isAbstract() && $method->getDeclaringClass()->name === $class->name) {
                /** @var DiscriminatorMapping $mapping */
                $mapping = $method->invoke(null);
                foreach ($mapping->getConfigs() as $config) {
                    if ($method->getDeclaringClass()->name === $this->class->name || $config->getClassName() === $this->class->name) {
                        $this->mergeChildClass($context, $config, $list, 'getModificationMetadata');
                    }
                }
            }
            $class = $class->getParentClass();
        }

        return new CompositeMetadata(new MetadataFieldHashmap($list), $this->class);
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        
        $class = $this->class;

        while ($class) {
            $method = $class->getMethod('getDiscriminatorMapping');
            if (!$method->isAbstract() && $method->getDeclaringClass()->name === $class->name) {
                /** @var DiscriminatorMapping $mapping */
                $mapping = $method->invoke(null);
                $list[$mapping->getPropertyName()] = new DiscriminatorColumn($mapping);
                foreach ($mapping->getConfigs() as $config) {
                    if ($method->getDeclaringClass()->name === $this->class->name || $config->getClassName() === $this->class->name) {
                        $this->mergeChildClass($context, $config, $list, 'getCreationMetadata');
                    }
                }
            }
            $class = $class->getParentClass();
        }

        return new CompositeMetadata(new MetadataFieldHashmap($list), $this->class);
    }

    public function getResultMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        
        $class = $this->class;

        while ($class) {
            $method = $class->getMethod('getDiscriminatorMapping');
            if (!$method->isAbstract() && $method->getDeclaringClass()->name === $class->name) {
                /** @var DiscriminatorMapping $mapping */
                $mapping = $method->invoke(null);
                $list[$mapping->getPropertyName()] = new DiscriminatorColumn($mapping);
                foreach ($mapping->getConfigs() as $config) {
                    if ($method->getDeclaringClass()->name === $this->class->name || $config->getClassName() === $this->class->name) {
                        $this->mergeChildClass($context, $config, $list, 'getResultMetadata');
                    }
                }
            }
            $class = $class->getParentClass();
        }

        return new CompositeMetadata(new MetadataFieldHashmap($list), $this->class);
    }

    /**
     * @param array<string, mixed> $list
     */
    private function mergeChildClass(
        ApieContext $context,
        DiscriminatorConfig $config,
        array& $list,
        string $method
    ): void {
        $refl = new ReflectionClass($config->getClassName());
        $tmp = new RegularObjectStrategy($refl);
        $mapping = $tmp->$method($context);
        foreach ($mapping->getHashmap() as $propertyName => $declaration) {
            if (isset($list[$propertyName])) {
                $list[$propertyName] = new OptionalField($declaration, $list[$propertyName]);
            } else {
                $list[$propertyName] = new OptionalField($declaration);
            }
        }
    }
}
