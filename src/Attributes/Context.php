<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * This is used on parameters to tell this argument comes from the context. If the context key is missing
 * the method is considered not allowed to run right now.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class Context
{
    public function __construct(public ?string $contextKey = null)
    {
    }
}
