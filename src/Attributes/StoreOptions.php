<?php

namespace Apie\Core\Attributes;

use Attribute;

/**
 * This attribute is for finetuning the generated database schema.
 *
 * - mutableListField: stores/restores mutability state of a list.
 * - alwaysMixedData: if true, the data is always stored in the special mixed data table.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class StoreOptions
{
    public function __construct(
        public readonly bool $mutableListField = false,
        public readonly bool $alwaysMixedData = false,
    ) {
    }
}
