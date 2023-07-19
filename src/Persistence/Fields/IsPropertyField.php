<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\IdentifierUtils;
use ReflectionClass;
use ReflectionProperty;

trait IsPropertyField
{
    /**
     * @param class-string<object> $declaredClass
     */
    public function __construct(private readonly string $declaredClass, private readonly string $propertyName)
    {
    }

    private function getProperty(): ReflectionProperty
    {
        return (new ReflectionClass($this->declaredClass))->getProperty($this->propertyName);
    }

    public function getName(): string
    {
        return 'apie_' . IdentifierUtils::propertyToUnderscore($this->getProperty());
    }
}
