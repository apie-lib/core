<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Used by DTO to indicate a field is optional.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Optional
{
}
