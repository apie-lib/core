<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

interface IdentifierInterface extends ValueObjectInterface
{
    public function toNative(): string|int|null;
    /**
     * @return RefectionClass<EntityInterface>
     */
    public static function getReferenceFor(): ReflectionClass;
}
