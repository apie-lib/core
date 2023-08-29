<?php
namespace Apie\Core\Persistence\Metadata;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\IdentifierUtils;
use Apie\Core\Persistence\Fields\AutoincrementInteger;
use Apie\Core\Persistence\Fields\ManyToEntityReference;
use Apie\Core\Persistence\Lists\PersistenceFieldList;
use Apie\Core\Persistence\PersistenceTableInterface;
use ReflectionClass;

class EntityIndexMetadata implements PersistenceTableInterface
{
    /**
     * @param class-string<EntityInterface> $class
     */
    public function __construct(
        private readonly BoundedContextId $boundedContextId,
        private readonly string $class
    ) {
    }
    
    public function getName(): string
    {
        return 'apie_index_' . $this->boundedContextId . '_' . IdentifierUtils::classNameToUnderscore(new ReflectionClass($this->class));
    }

    public function getOriginalClass(): ?string
    {
        return null;
    }

    public function getFields(): PersistenceFieldList
    {
        return new PersistenceFieldList([
            new AutoincrementInteger(),
            new ManyToEntityReference('entity', $this->class, $this->boundedContextId),
        ]);
    }
}
