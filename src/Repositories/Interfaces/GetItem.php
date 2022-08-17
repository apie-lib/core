<?php
namespace Apie\Core\Repositories\Interfaces;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Repositories\Search\QuerySearch;

/**
 * @template T of EntityInterface
 */
interface GetItem
{
    /**
     * @return T
     */
    public function __invoke(int $index, QuerySearch $search): EntityInterface;
}
