<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Mark a resource to overwrite it after persisting. Normally we do not do this for performance reasons,
 * but for example if we have an auto-incrementing id, we need to do this to get the id back in the object
 * after persisting it.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class OverwriteAfterPersist
{
}
