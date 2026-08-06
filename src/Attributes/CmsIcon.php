<?php

namespace Apie\Core\Attributes;

/**
 * Define an icon for the CMs to display
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class CmsIcon
{
    public function __construct(public readonly string $icon)
    {
    }
}
