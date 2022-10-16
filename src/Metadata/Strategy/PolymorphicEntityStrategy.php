<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Entities\PolymorphicEntityInterface;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\CompositeMetadata;
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
        $required = [];
        
        $class = $this->class;

        while ($class) {
            $method = $class->getMethod('getDiscriminatorMapping');
            if (!$method->isAbstract() && $method->getDeclaringClass()->name === $class->name) {
                /** @var DiscriminatorMapping $mapping */
                $mapping = $method->invoke(null);
                foreach ($mapping->getConfigs() as $config) {
                    $ignored = [];
                    if ($method->getDeclaringClass()->name === $this->class->name || $config->getClassName() === $this->class->name) {
                        $this->mergeChildClass($context, $config, $list, $ignored, 'getModificationMetadata');
                    }
                }
            }
            $class = $class->getParentClass();
        }

        return new CompositeMetadata(new ReflectionHashmap($list), new StringList($required));
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $list = [];
        $discriminatorColumns = [];
        $required = [];
        
        $class = $this->class;

        while ($class) {
            $method = $class->getMethod('getDiscriminatorMapping');
            if (!$method->isAbstract() && $method->getDeclaringClass()->name === $class->name) {
                /** @var DiscriminatorMapping $mapping */
                $mapping = $method->invoke(null);
                $list[$mapping->getPropertyName()] = $mapping;
                $discriminatorColumns[$mapping->getPropertyName()] = $mapping->getPropertyName();
                foreach ($mapping->getConfigs() as $config) {
                    if ($method->getDeclaringClass()->name === $this->class->name || $config->getClassName() === $this->class->name) {
                        $this->mergeChildClass($context, $config, $list, $required, 'getCreationMetadata');
                    }
                }
            }
            $class = $class->getParentClass();
        }

        return new CompositeMetadata(new ReflectionHashmap($list), new StringList([...$discriminatorColumns, ...$required]));
    }

    /**
     * @param array<string, mixed> $list
     * @param array<string, string> $required
     */
    private function mergeChildClass(
        ApieContext $context,
        DiscriminatorConfig $config,
        array& $list,
        array& $required,
        string $method
    ): void {
        $refl = new ReflectionClass($config->getClassName());
        $tmp = new RegularObjectStrategy($refl);
        $mapping = $tmp->$method($context);
        foreach ($mapping->getHashmap() as $propertyName => $declaration) {
            // TODO merge types...
            $list[$propertyName] = $declaration;
        }
        $requiredInChild = $mapping->getRequiredFields()->toArray();
        $hashmap = array_combine($requiredInChild, $requiredInChild);
        foreach ($required as $requiredString) {
            if (!isset($hashmap[$requiredString])) {
                unset($required[$requiredString]);
            }
        }
    }
}
