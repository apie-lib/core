<?php

namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\SingleLetter;
use Apie\Fixtures\TestHelpers\ValueObjectTestCase;

class SingleLetterTest extends ValueObjectTestCase
{

    public static function className(): string
    {
        return SingleLetter::class;
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'type' => 'string',
            'format' => 'singleletter',
            'pattern' => true,
        ];
    }

    public static function provideFromNative(): array
    {
        return [
            'single character' => ['a', 'a'],
        ];
    }
}
