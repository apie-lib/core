<?php
namespace Apie\Core\Repositories\Interfaces;

use Apie\Core\Repositories\Search\QuerySearch;

interface CountItems
{
    public function __invoke(QuerySearch $search): int;
}
