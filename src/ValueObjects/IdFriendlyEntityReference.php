<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\ExampleValue;

#[ExampleValue('domain_resource_123')]
class IdFriendlyEntityReference extends EntityReference
{
    protected static function getSeparator(): string
    {
        return '_';
    }
}
