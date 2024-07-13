<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Multipart content type is allowed (in particular used by file uploads).
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class AllowMultipart
{
}
