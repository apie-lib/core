<?php
namespace Apie\Tests\Core\BoundedContext;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Fixtures\TestHelpers\ObjectTestCase;

class BoundedContextTest extends ObjectTestCase
{
    public static function className(): string
    {
        return BoundedContext::class;
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'required' => ['id', 'resources', 'actions'],
            'type' => 'object',
            'properties' => [
                'id' => [
                    'oneOf' => [
                        [
                            '$ref' => '#/components/schemas/BoundedContextId-post'
                        ],
                        [
                            'type' => 'string'
                        ]
                    ],
                    'nullable' => false,
                ],
                'resources' => [
                    '$ref' => '#/components/schemas/ReflectionClassList-post'
                ],
                'actions' => [
                    '$ref' => '#/components/schemas/ReflectionMethodList-post'
                ],
            ],
        ];
    }
}