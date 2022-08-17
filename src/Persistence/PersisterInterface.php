<?php
namespace Apie\Core\Persistence;

use Apie\Core\Entities\EntityInterface;

interface PersisterInterface
{
    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistNew(EntityInterface $entity): EntityInterface;

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @return T
     */
    public function persistExisting(EntityInterface $entity): EntityInterface;
}
