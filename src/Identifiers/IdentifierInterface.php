<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;

/**
 * @template T of EntityInterface
 */
interface IdentifierInterface extends ValueObjectInterface
{
    public function toNative(): string|int|null;

    /**
     * @return ReflectionClass<T>
     */
    public static function getReferenceFor(): ReflectionClass;
}
