<?php
namespace Apie\Tests\Core\BoundedContext;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Fixtures\TestHelpers\ObjectTestCase;

class BoundedContextHashmapTest extends ObjectTestCase
{
    public static function className(): string
    {
        return BoundedContextHashmap::class;
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => [
                '$ref' => '#/components/schemas/BoundedContext-post'
            ]
        ];
    }
}