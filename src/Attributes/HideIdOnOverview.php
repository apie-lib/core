<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Mark a resource to hide the 'id' column. We can not disable it with RuntimeCheck as it would disable row clicks.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class HideIdOnOverview
{
}
