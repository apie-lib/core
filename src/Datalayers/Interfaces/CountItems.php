<?php
namespace Apie\Core\Datalayers\Interfaces;

use Apie\Core\Datalayers\Search\QuerySearch;

interface CountItems
{
    public function __invoke(QuerySearch $search): int;
}
