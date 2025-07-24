<?php

namespace Apie\Core\Attributes;

use Attribute;

/**
 * This attribute is for finetuning the generated database schema.
 * 
 * - nullableField: name of field that stores if a field is null.
 *   Used for nullable lists to map null and empty lists.
 * - mutableListField: stores/restores mutability state of a list.
 * - alwaysMixedData: if true, the data is always stored in the special mixed data table.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class StoreOptions
{
    public function __construct(
        public readonly ?string $nullableField = null,
        public readonly ?string $mutableListField = null,
        public readonly bool $alwaysMixedData = false,
    ) {
    }
}
