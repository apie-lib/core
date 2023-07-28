<?php
namespace Apie\Core\Persistence;

use Apie\Core\Persistence\Enums\PersistenceColumn;
use ReflectionType;

interface PersistenceFieldInterface
{
    public function getName(): string;
    public function getDeclaredClass(): ?string;
    public function isAllowsNull(): bool;
    public function getType(): ReflectionType;
    public function getPersistenceType(): PersistenceColumn;
}
