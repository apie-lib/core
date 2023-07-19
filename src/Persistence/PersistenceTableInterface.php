<?php
namespace Apie\Core\Persistence;

use Apie\Core\Persistence\Lists\PersistenceFieldList;

interface PersistenceTableInterface
{
    public function getName(): string;
    public function getFields(): PersistenceFieldList;
}
