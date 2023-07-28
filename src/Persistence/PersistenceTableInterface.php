<?php
namespace Apie\Core\Persistence;

use Apie\Core\Persistence\Lists\PersistenceFieldList;

interface PersistenceTableInterface
{
    public function getName(): string;
    /**
     * @return class-string<object>|null
     */
    public function getOriginalClass(): ?string;
    public function getFields(): PersistenceFieldList;
}
