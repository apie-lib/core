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
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionDatalayer implements ApieDatalayer
{
    /**
     * @var array<string, array<string, array<int, EntityInterface>>>
     */
    private array $stored = [];

    /**
     * @var array<string, array<class-string<EntityInterface>, EntityListInterface<EntityInterface>>>
     */
    private array $alreadyLoadedLists = [];

    private Generator $generator;

    public function __construct(
        private readonly SessionInterface $session,
        private readonly LazyLoadedListFilterer $filterer,
        private readonly string $sessionKey = 'datalayer'
    ) {
        $this->generator = Factory::create();
        $this->stored = $this->restore();
    }

    /**
     * @return array<string, array<string, array<int, EntityInterface>>>
     */
    protected function restore(): array
    {
        return $this->session->get($this->sessionKey, []);
    }

    /**
     * @param array<string, array<string, array<int, EntityInterface>>> $list
     */
    protected function store(array $list): void
    {
        $this->session->set($this->sessionKey, $list);
    }

    public function all(ReflectionClass $class, ?BoundedContextId $boundedContextId = null): EntityListInterface
    {
        $boundedContextId ??= new BoundedContextId('unknown');
        $boundedContextIdString = $boundedContextId->toNative();
        $className = $class->name;
        $this->stored[$boundedContextIdString][$className] ??= [];
        if (!isset($this->alreadyLoadedLists[$boundedContextIdString][$className])) {
            $this->alreadyLoadedLists[$boundedContextIdString][$className] = new InMemoryEntityList(
                $class,
                $boundedContextId,
                $this->filterer,
                $this->stored[$boundedContextIdString][$className]
            );
        }
        return $this->alreadyLoadedLists[$boundedContextIdString][$className];
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        $boundedContextId ??= new BoundedContextId('unknown');
        $boundedContextIdString = $boundedContextId->toNative();
        $id = $entity->getId();
        if ($id instanceof AutoIncrementInteger) {
            $id = $id::createRandom($this->generator);
            $reflProperty = new ReflectionProperty($entity, 'id');
            $reflProperty->setValue($entity, $id);
        }
        $className = $id::getReferenceFor()->name;
        $id = $entity->getId()->toNative();
        foreach ($this->stored[$boundedContextIdString][$className] ?? [] as $entityInList) {
            if ($entityInList->getId()->toNative() === $id) {
                throw new EntityAlreadyPersisted($entity);
            }
        }
        $this->stored[$boundedContextIdString][$className][] = $entity;
        $this->store($this->stored);
        return $entity;
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        $boundedContextId ??= new BoundedContextId('unknown');
        $boundedContextIdString = $boundedContextId->toNative();
        $id = $entity->getId()->toNative();
        $className = $entity->getId()::getReferenceFor()->name;
        foreach ($this->stored[$boundedContextIdString][$className] ?? [] as $key => $entityInList) {
            if ($entityInList->getId()->toNative() === $id) {
                $this->stored[$className][$key] = $entity;
                $this->store($this->stored);
                return $entity;
            }
        }
        throw new UnknownExistingEntityError($entity);
    }

    public function find(IdentifierInterface $identifier, ?BoundedContextId $boundedContextId = null): EntityInterface
    {
        $boundedContextId ??= new BoundedContextId('unknown');
        $boundedContextIdString = $boundedContextId->toNative();
        $className = $identifier::getReferenceFor()->name;
        $id = $identifier->toNative();
        foreach ($this->stored[$boundedContextIdString][$className] ?? [] as $entityInList) {
            if ($entityInList->getId()->toNative() === $id) {
                return $entityInList;
            }
        }
        throw new EntityNotFoundException($identifier);
    }

    public function removeExisting(EntityInterface $entity, ?BoundedContextId $boundedContextId = null): void
    {
        $boundedContextId ??= new BoundedContextId('unknown');
        $boundedContextIdString = $boundedContextId->toNative();
        $identifier = $entity->getId();
        $className = $identifier::getReferenceFor()->name;
        $id = $identifier->toNative();
        $newList = [];
        foreach ($this->stored[$boundedContextIdString][$className] ?? [] as $entityInList) {
            if ($entityInList->getId()->toNative() !== $id) {
                $newList[] = $entityInList;
            }
        }
        $this->stored[$boundedContextIdString][$className] = $newList;
        $this->store($this->stored);
    }

    public function upsert(EntityInterface $entity, ?BoundedContextId $boundedContextId): EntityInterface
    {
        try {
            return $this->persistExisting($entity, $boundedContextId);
        } catch (UnknownExistingEntityError) {
            return $this->persistNew($entity, $boundedContextId);
        }
    }
}
