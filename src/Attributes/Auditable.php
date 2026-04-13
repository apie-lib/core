<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Add this attribute to tell Apie to audit this entity.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Auditable
{
    public function __construct(
        public RuntimeCheck $permission = new RuntimeCheck(),
        public bool $readEvents = false,
        public bool $readAllEvents = false,
    ) {
    }
}
