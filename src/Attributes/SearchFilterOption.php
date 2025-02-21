<?php

namespace Apie\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_METHOD|Attribute::TARGET_CLASS)]
class SearchFilterOption
{
    public function __construct(
        public readonly bool $enabled = true
    ) {
    }
}
