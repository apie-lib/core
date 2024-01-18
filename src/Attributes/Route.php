<?php
namespace Apie\Core\Attributes;

use Apie\Core\Enums\RequestMethod;
use Attribute;

/**
 * For Apie actions, provide a Route url.
 */
#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
final class Route
{
    public const CMS = 'cms';
    public const ALL = 'all';
    public const API = 'api';

    public function __construct(
        public readonly string $routeDefinition,
        public readonly ?RequestMethod $requestMethod = null,
        public readonly string $target = self::ALL
    ) {
    }
}
