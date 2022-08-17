<?php
namespace Apie\Core\Repositories\InMemory;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Exceptions\EntityAlreadyPersisted;
use Apie\Core\Exceptions\EntityNotFoundException;
use Apie\Core\Exceptions\UnknownExistingEntityError;
use Apie\Core\Identifiers\AutoIncrementInteger;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\Persistence\PersisterInterface;
use Apie\Core\Repositories\ApieRepository;
use Apie\Core\Repositories\Lists\LazyLoadedList;
use Apie\Core\Repositories\ValueObjects\LazyLoadedListIdentifier;
use Faker\Factory;
use Faker\Generator;
use ReflectionClass;
use ReflectionProperty;

class InMemoryRepository implements ApieRepository, PersisterInterface
{
    /**
     * @var array<string, array<int, EntityInterface>>
     */
    private array $stored = [];

    /**
     * @var array<class-string<EntityInterface>, LazyLoadedList<EntityInterface>>
     */
    private array $alreadyLoadedLists = [];

    private Generator $generator;

    public function __construct(private BoundedContextId $boundedContextId)
    {
        $this->generator = Factory::create();
    }

    public function all(ReflectionClass $class): LazyLoadedList
    {
        $className = $class->name;
        if (!isset($this->alreadyLoadedLists[$className])) {
            $this->stored[$className] = [];
            $this->alreadyLoadedLists[$className] = new LazyLoadedList(
                LazyLoadedListIdentifier::createFrom($this->boundedContextId, $class),
                new GetFromArray($this->stored[$className]),
                new TakeFromArray($this->stored[$className]),
                new CountArray($this->stored[$className])
            );
        }
        return $this->alreadyLoadedLists[$className];
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity): EntityInterface
    {
        $id = $entity->getId();
        if ($id instanceof AutoIncrementInteger) {
            $id = $id::createRandom($this->generator);
        }
        $reflProperty = new ReflectionProperty($entity, 'id');
        $reflProperty->setAccessible(true);
        $reflProperty->setValue($entity, $id);
        $className = $id::getReferenceFor()->name;
        $id = $entity->getId()->toNative();
        $className = $entity->getId()::getReferenceFor()->name;
        foreach ($this->stored[$className] ?? [] as $key => $entityInList) {
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
    public function persistExisting(EntityInterface $entity): EntityInterface
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

    public function find(IdentifierInterface $identifier): EntityInterface
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
}
