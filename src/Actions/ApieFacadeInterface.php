<?php
namespace Apie\Core\Actions;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;
use ReflectionClass;
use ReflectionMethod;

interface ApieFacadeInterface
{
    /**
     * @template T of EntityInterface
     * @param class-string<T>|ReflectionClass<T> $class
     * @return EntityListInterface<T>
     */
    public function all(string|ReflectionClass $class, BoundedContext|BoundedContextId $boundedContext): EntityListInterface;

    /**
     * @template T of EntityInterface
     * @param IdentifierInterface<T> $identifier
     * @return T
     */
    public function find(IdentifierInterface $identifier, BoundedContext|BoundedContextId $boundedContext): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity, BoundedContext|BoundedContextId $boundedContext): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity, BoundedContext|BoundedContextId $boundedContext): EntityInterface;

    /**
    * @template T of EntityInterface
    * @param T $entity
    * @return T
    */
    public function upsert(EntityInterface $entity, BoundedContext|BoundedContextId $boundedContext): EntityInterface;

    public function removeExisting(EntityInterface $entity, BoundedContext|BoundedContextId $boundedContext): void;

    public function normalize(mixed $object, ApieContext $apieContext): string|int|float|bool|ItemList|ItemHashmap|null;

    /**
     * @param string|int|float|bool|ItemList<mixed>|ItemHashmap<mixed>|array<string, mixed>|null $object
     */
    public function denormalizeNewObject(string|int|float|bool|ItemList|ItemHashmap|array|null $object, string $desiredType, ApieContext $apieContext): mixed;

    /**
     * @template T of object
     * @param T $existingObject
     * @return T
     */
    public function denormalizeOnExistingObject(ItemHashmap $object, object $existingObject, ApieContext $apieContext): mixed;
    
    /**
     * @param string|int|float|bool|ItemList<mixed>|ItemHashmap<mixed>|array<string, mixed>|null $input
     */
    public function denormalizeOnMethodCall(string|int|float|bool|ItemList|ItemHashmap|array|null $input, ?object $object, ReflectionMethod $method, ApieContext $apieContext): mixed;

    public function createAction(ApieContext $apieContext): ActionInterface;
}
