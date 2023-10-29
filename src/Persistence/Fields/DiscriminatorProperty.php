<?php
namespace Apie\Core\Persistence\Fields;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use Apie\Core\Persistence\PersistenceFieldInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use ReflectionType;

final class DiscriminatorProperty implements PersistenceFieldInterface
{
    public function getDeclaredClass(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return 'discriminator';
    }

    public function getType(): ReflectionType
    {
        return ReflectionTypeFactory::createReflectionType('array');
    }

    public function isAllowsNull(): bool
    {
        return true;
    }

    public function getPersistenceType(): PersistenceColumn
    {
        return PersistenceColumn::createFromType($this->getType());
    }
}
