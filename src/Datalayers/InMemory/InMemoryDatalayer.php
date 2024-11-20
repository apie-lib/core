<?php
namespace Apie\Core\Datalayers\InMemory;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Datalayers\Lists\EntityListInterface;
use Apie\Core\Datalayers\Lists\InMemoryEntityList;
use Apie\Core\Datalayers\Search\LazyLoadedListFilterer;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Exceptions\EntityAlreadyPersisted;
use Apie\Core\Exceptions\EntityNotFoundException;
use Apie\Core\Exceptions\UnknownExistingEntityError;
use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\Identifiers\IdentifierInterface;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionProperty;

class InMemoryDatalayer implements ApieDatalayer
{
    /**
     * @var array<string, array<int, EntityInterface>>
     */
    private array $stored = [];

    /**
     * @var array<class-string<EntityInterface>, EntityListInterface<EntityInterface>>
     */
    private array $alreadyLoadedLists = [];

    private Generator $generator;

    public function __construct(private BoundedContextId $boundedContextId, private LazyLoadedListFilterer $filterer)
    {
        $this->generator = Factory::create();
    }

    public function all(ReflectionClass $class, ?BoundedContextId $boundedContextId = null): EntityListInterface
    {
        $className = $class->name;
        $this->stored[$className] ??= [];
        if (!isset($this->alreadyLoadedLists[$className])) {
            $this->alreadyLoadedLists[$className] = new InMemoryEntityList(
                $class,
                $this->boundedContextId,
                $this->filterer,
                $this->stored[$className]
            );
        }
        return $this->alreadyLoadedLists[$className];
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        $id = $entity->getId();
        if ($id instanceof AutoIncrementInteger) {
            $id = $id::createRandom($this->generator);
            $reflProperty = new ReflectionProperty($entity, 'id');
            $reflProperty->setValue($entity, $id);
        }
        $className = $id::getReferenceFor()->name;
        $id = $entity->getId()->toNative();
        $className = $entity->getId()::getReferenceFor()->name;
        foreach ($this->stored[$className] ?? [] as $entityInList) {
            if ($entityInList->getId()->toNative() === $id) {
                throw new EntityAlreadyPersisted($entity);
            }
        }
        $this->stored[$className][] = $entity;
        return $entity;
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        $className = get_class($entity);
        $id = $entity->getId()->toNative();
        $className = $entity->getId()::getReferenceFor()->name;
        foreach ($this->stored[$className] ?? [] as $key => $entityInList) {
            if ($entityInList->getId()->toNative() === $id) {
                $this->stored[$className][$key] = $entity;
                return $entity;
            }
        }
        throw new UnknownExistingEntityError($entity);
    }

    public function find(IdentifierInterface $identifier, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        $className = $identifier::getReferenceFor()->name;
        $id = $identifier->toNative();
        foreach ($this->stored[$className] ?? [] as $entityInList) {
            if ($entityInList->getId()->toNative() === $id) {
                return $entityInList;
            }
        }
        throw new EntityNotFoundException($identifier);
    }

    public function removeExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): void
    {
        $identifier = $entity->getId();
        $className = $identifier::getReferenceFor()->name;
        $id = $identifier->toNative();
        $newList = [];
        foreach ($this->stored[$className] ?? [] as $entityInList) {
            if ($entityInList->getId()->toNative() !== $id) {
                $newList[] = $entityInList;
            }
        }
        $this->stored[$className] = $newList;
    }
}
