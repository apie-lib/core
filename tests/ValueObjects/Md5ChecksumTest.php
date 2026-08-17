<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Fixtures\TestHelpers\TestsValueObjectConstructor;
use Apie\Fixtures\TestHelpers\ValueObjectTestCase;

class Md5ChecksumTest extends ValueObjectTestCase
{
    use TestsValueObjectConstructor;

    public static function className(): string
    {
        return \Apie\Core\ValueObjects\Md5Checksum::class;
    }

    public static function provideFromNative(): array
    {
        return [
            ['d41d8cd98f00b204e9800998ecf8427e', 'D41D8CD98F00B204E9800998ECF8427E'],
            ['d41d8cd98f00b204e9800998ecf8427e', 'd41d8cd98f00b204e9800998ecf8427e'],
        ];
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'type' => 'string',
            'pattern' => true,
            'description' => 'Represents a md5 checksum',
            'format' => 'md5checksum'
        ];
    }
}
