<?php
namespace Apie\Core\Datalayers\InMemory;

use Apie\Core\ApieLib;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Datalayers\DataLayerMetadata;
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

    /** @var array<string, array<class-string<EntityInterface>, array<string, DataLayerMetadata>>> */
    private array $metadata = [];

    public function __construct(
        private readonly SessionInterface $session,
        private readonly LazyLoadedListFilterer $filterer,
        private readonly string $sessionKey = 'datalayer'
    ) {
        $this->generator = Factory::create();
        $this->stored = $this->restore();
        $this->metadata = $this->session->get($this->sessionKey . '_metadata', []);
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
        $now = ApieLib::getPsrClock()->now();
        $this->metadata[$boundedContextIdString][$className][(string) $id] = new DataLayerMetadata(
            $now,
            $now,
            ApieLib::VERSION,
            ApieLib::VERSION
        );
        $this->storeMetadata();
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
                $this->stored[$boundedContextIdString][$className][$key] = $entity;
                $this->store($this->stored);
                $metadata = $this->metadata[$boundedContextIdString][$className][(string) $id] ?? new DataLayerMetadata(
                    ApieLib::getPsrClock()->now(),
                    ApieLib::getPsrClock()->now(),
                    ApieLib::VERSION,
                    ApieLib::VERSION
                );
                $this->metadata[$boundedContextIdString][$className][(string) $id] = $metadata->withUpdatedMetadata(
                    ApieLib::getPsrClock()->now(),
                    ApieLib::VERSION
                );
                $this->storeMetadata();
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

    /**
     * @template T of EntityInterface
     * @param IdentifierInterface<T> $identifier
     */
    public function getMetadata(
        IdentifierInterface $identifier,
        ?BoundedContextId $boundedContextId = null
    ): DataLayerMetadata {
        $boundedContextId ??= new BoundedContextId('unknown');
        $this->find($identifier, $boundedContextId);
        $contextId = $boundedContextId->toNative();
        $className = $identifier::getReferenceFor()->name;
        $id = (string) $identifier->toNative();
        if (!isset($this->metadata[$contextId][$className][$id])) {
            $now = ApieLib::getPsrClock()->now();
            $this->metadata[$contextId][$className][$id] = new DataLayerMetadata(
                $now,
                $now,
                ApieLib::VERSION,
                ApieLib::VERSION
            );
            $this->storeMetadata();
        }

        return $this->metadata[$contextId][$className][$id];
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
        unset($this->metadata[$boundedContextIdString][$className][(string) $id]);
        $this->store($this->stored);
        $this->storeMetadata();
    }

    public function upsert(EntityInterface $entity, ?BoundedContextId $boundedContextId): EntityInterface
    {
        try {
            return $this->persistExisting($entity, $boundedContextId);
        } catch (UnknownExistingEntityError) {
            return $this->persistNew($entity, $boundedContextId);
        }
    }

    private function storeMetadata(): void
    {
        $this->session->set($this->sessionKey . '_metadata', $this->metadata);
    }
}
