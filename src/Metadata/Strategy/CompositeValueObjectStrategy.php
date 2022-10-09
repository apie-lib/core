<?php
namespace Apie\Core\Metadata\Strategy;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\Context\ApieContext;
use Apie\Core\Context\ReflectionHashmap;
use Apie\Core\Lists\StringList;
use Apie\Core\Metadata\CompositeMetadata;
use Apie\Core\Metadata\StrategyInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\HtmlBuilders\Factories\ReflectionTypeFactory;
use ReflectionClass;

final class CompositeValueObjectStrategy implements StrategyInterface
{
    public static function supports(ReflectionClass $class): bool
    {
        return $class->implementsInterface(ValueObjectInterface::class)
            && in_array(CompositeValueObject::class, $class->getTraitNames());
    }

    /**
     * @param ReflectionClass<ValueObjectInterface> $class
     */
    public function __construct(private ReflectionClass $class)
    {
    }

    public function getCreationMetadata(ApieContext $context): CompositeMetadata
    {
        $method = $this->class->getMethod('getFields');
        $map = [];
        $required = [];
        foreach ($method->invoke(null) as $name => $field) {
            $map[$name] = ReflectionTypeFactory::createReflectionType($field->getTypehint());
            if (!$field->isOptional()) {
                $required[] = $name;
            }
        }
        return new CompositeMetadata(new ReflectionHashmap($map), new StringList($required));
    }
}
