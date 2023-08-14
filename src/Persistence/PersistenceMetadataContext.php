<?php
namespace Apie\Core\Persistence;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\IdentifierUtils;
use Apie\Core\Persistence\Fields\FieldInvariant;
use Apie\Core\Persistence\Lists\PersistenceFieldList;
use Apie\Core\Persistence\Lists\PersistenceTableList;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;

final class PersistenceMetadataContext
{
    /** @var array<int, PersistenceTableInterface> */
    private array $tables = [];

    /** @var array<int, ReflectionProperty> */
    private array $visited = [];

    /** @var array<int, PersistenceFieldInterface> */
    private array $invariants = [];

    /**
     * @var ReflectionClass<object>|null
     */
    private ?ReflectionClass $currentObject = null;

    private ?BoundedContext $currentBoundedContext = null;

    private ?PersistenceFieldInterface $currentIdentifier = null;

    private string $invariantPrefix = '';

    public function __construct(private readonly PersistenceMetadataFactoryInterface $factory)
    {
    }

    /**
     * @param ReflectionClass<object>|null $currentObject
     */
    public function useContext(
        ?ReflectionClass $currentObject,
        ?PersistenceFieldInterface $currentIdentifier,
        ?BoundedContext $currentBoundedContext
    ): self {
        $result = clone $this;
        $result->tables = &$this->tables;
        $result->currentObject = $currentObject;
        $result->currentIdentifier = $currentIdentifier;
        $result->currentBoundedContext = $currentBoundedContext;
        return $result;
    }

    /**
     * @return ReflectionClass<object>|null
     */
    public function getOriginalObject(): ?ReflectionClass
    {
        if (empty($this->visited)) {
            return null;
        }

        return reset($this->visited)->getDeclaringClass();
    }

    /**
     * @return ReflectionClass<object>|null
     */
    public function getCurrentObject(): ?ReflectionClass
    {
        return $this->currentObject;
    }

    public function getCurrentIdentifier(): ?PersistenceFieldInterface
    {
        return $this->currentIdentifier;
    }

    public function getCurrentBoundedContext(): ?BoundedContext
    {
        return $this->currentBoundedContext;
    }

    public function getCurrentProperty(): ?ReflectionProperty
    {
        return $this->visited[count($this->visited) - 1] ?? null;
    }

    public function getCurrentPropertyType(): ?ReflectionType
    {
        $property = $this->getCurrentProperty();
        if ($property) {
            return $property->getType();
        }

        return null;
    }

    /**
     * @return ReflectionClass<object>|null
     */
    public function getCurrentPropertyClass(): ?ReflectionClass
    {
        $type = $this->getCurrentPropertyType();
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            return new ReflectionClass($type->getName());
        }

        return null;
    }

    public function addFieldInvariant(PersistenceFieldInterface $field, ?string $invariantName = null): self
    {
        if ($invariantName === null) {
            $property = $this->getCurrentProperty();
            $invariantName = $property ? IdentifierUtils::propertyToUnderscore($property) : 'unknown';
        }
        $result = clone $this;
        $result->tables = &$this->tables;
        $result->invariants[] = new FieldInvariant($field);
        if (empty($result->invariantPrefix)) {
            $result->invariantPrefix = '__' . $invariantName;
        } else {
            $result->invariantPrefix .= '__' . $invariantName;
        }
        return $result;
    }

    public function getInvariantPrefix(): string
    {
        return $this->invariantPrefix;
    }

    public function getInvariantFields(): PersistenceFieldList
    {
        return new PersistenceFieldList($this->invariants);
    }

    /**
     * @internal
     */
    public function verify(PersistenceMetadataFactoryInterface $factory): self
    {
        assert($factory === $this->factory);
        return $this;
    }

    public function getTables(): PersistenceTableList
    {
        return new PersistenceTableList($this->tables);
    }

    public function addPersistenceTable(PersistenceTableInterface $persistenceTable): self
    {
        $this->tables[] = $persistenceTable;
        return $this;
    }

    /**
     * @param ReflectionClass<object> $class
     * @return array<int, PersistenceMetadataContext>
     */
    public function getProperties(ReflectionClass $class, bool $privateOnly = false): array
    {
        $result = [];
        foreach ($class->getProperties($privateOnly ? ReflectionMethod::IS_PRIVATE : null) as $property) {
            if (!$property->isStatic()) {
                $result[] = $this->visitProperty($property);
            }
        }
        $parentClass = $class->getParentClass();
        if ($parentClass) {
            $result = array_merge(
                $result,
                $this->getProperties(
                    $parentClass,
                    true
                )
            );
        }

        return $result;
    }

    public function visitProperty(ReflectionProperty $property): self
    {
        // TODO: inf recursion check.
        $result = clone $this;
        $result->tables = &$this->tables;
        $result->visited[] = $property;
        return $result;
    }

    public function visitClass(): self
    {
        // TODO: inf recursion check.
        $result = clone $this;
        $result->tables = &$this->tables;
        $result->currentObject = $this->getCurrentPropertyClass();
        return $result;
    }

    public function createPropertyDefinition(): ?PersistenceFieldInterface
    {
        return $this->factory->createProperty($this);
    }

    public function createTableDefinition(): PersistenceTableInterface
    {
        $tableDefinition = $this->factory->createInvariantTable($this->getCurrentPropertyClass(), $this);
        $this->tables[] = $tableDefinition;

        return $tableDefinition;
    }
}
