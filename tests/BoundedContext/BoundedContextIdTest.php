<?php

namespace Apie\Tests\Core\BoundedContext;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Fixtures\TestHelpers\ValueObjectTestCase;

class BoundedContextIdTest extends ValueObjectTestCase
{

    public static function className(): string
    {
        return BoundedContextId::class;
    }

    public static function provideFromNative(): array
    {
        return [
            'regular case' => ['test', 'test'],
        ];
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'type' => 'string',
            'format' => 'boundedcontextid',
            'pattern' => true,
        ];
    }
}