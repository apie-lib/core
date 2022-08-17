<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\Identifiers\Identifier;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

/**
 * @implements IdentifierInterface<BoundedContext>
 */
class BoundedContextId extends Identifier implements IdentifierInterface
{
    public static function getReferenceFor(): ReflectionClass
    {
        return new ReflectionClass(BoundedContext::class);
    }
}
