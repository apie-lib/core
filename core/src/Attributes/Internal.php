<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Mark a property/method/class or anything as internal and try to hide it from any automated process.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Internal
{
}
