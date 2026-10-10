<?php
namespace Apie\Core\Attributes;

use Apie\Core\Enums\RequestMethod;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class ActionOptions
{
    /**
     * @param array<string, mixed> $placeholders
     */
    public function __construct(
        public readonly ?string $pathRemainder = null,
        public readonly ?RequestMethod $requestMethod = null,
        public readonly array $placeholders = [],
    ) {
    }
}
