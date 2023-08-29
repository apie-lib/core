<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\IdentifierUtils;
use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use Doctrine\Common\Collections\Collection;
use ReflectionClass;
use ReflectionType;

class IndexTableReference implements PersistenceFieldInterface
{
    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function __construct(private readonly ReflectionClass $class, private readonly BoundedContextId $boundedContextId)
    {
    }

    public function getName(): string
    {
        return '_indexTable';
    }
    public function getDeclaredClass(): ?string
    {
        return null;
    }
    public function isAllowsNull(): bool
    {
        return false;
    }
    public function getType(): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType(Collection::class);
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::RELATION;
    }

    public function getTargetEntity(): string
    {
        return 'apie_index_' . $this->boundedContextId . '_' . IdentifierUtils::classNameToUnderscore($this->class);
    }
}
