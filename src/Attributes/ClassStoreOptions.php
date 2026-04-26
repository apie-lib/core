<?php

namespace Apie\Core\Attributes;

use Apie\Core\Enums\DefaultColumnName;
use Apie\Core\Enums\SortingOrder;
use Attribute;

/**
 * This attribute is for finetuning the datalayer
 */
#[Attribute(Attribute::TARGET_CLASS)]
class ClassStoreOptions
{
    public function __construct(
        public readonly DefaultColumnName $defaultColumnName = DefaultColumnName::CreatedAt,
        public readonly SortingOrder $defaultSortingOrder = SortingOrder::Descending,
    ) {
    }
}
