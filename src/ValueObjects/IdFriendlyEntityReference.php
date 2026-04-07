<?php
namespace Apie\Core\ValueObjects;

class IdFriendlyEntityReference extends EntityReference
{
    protected static function getSeparator(): string
    {
        return '_';
    }
}
