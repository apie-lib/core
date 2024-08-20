<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\JsonFileUpload;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Fixtures\TestHelpers\TestWithOpenapiSchema;
use Apie\Serializer\Exceptions\ValidationException;
use cebe\openapi\spec\Reference;
use LogicException;
use PHPUnit\Framework\TestCase;

class JsonFileUploadTest extends TestCase
{
    use TestWithFaker;
    use TestWithOpenapiSchema;

    /**
     * @test
     * @dataProvider inputProvider
     */
    public function fromNative_works_as_intended(array $input)
    {
        $testItem = JsonFileUpload::fromNative($input);
        $this->assertEquals($input, $testItem->toNative());
    }

    public function inputProvider()
    {
        yield 'empty file' => [['contents' => '', 'originalFilename' => 'empty.txt']];
        yield 'empty file, mime null' => [['contents' => '', 'originalFilename' => 'empty.txt', 'mime' => null]];
        yield 'base64 encoded' => [['base64' => 'AAaa', 'originalFilename' => 'base64.txt']];
        yield 'with mime type' => [['contents' => 'Hello', 'originalFilename' => 'hello.txt', 'mime' => 'text/plain']];
    }

    /**
     * @test
     * @dataProvider invalidProvider
     */
    public function it_refuses_invalid_input(string $expectedExceptionClass, string $expectedExceptionMessage, array $input)
    {
        // TODO move validationexception to apie/core
        if (!class_exists($expectedExceptionClass)) {
            $this->markTestSkipped('Class ' . $expectedExceptionClass . ' does not exist');
        }
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);
        JsonFileUpload::fromNative($input);
    }

    public function invalidProvider()
    {
        yield 'empty array' => [
            ValidationException::class,
            'Validation error:  Type "(missing value)" is not expected, expected Apie\Core\ValueObjects\Filename',
            []
        ];
        yield 'no content or base64' => [
            LogicException::class,
            'I need either a "contents" or a "base64" property',
            ['originalFilename' => 'tmp.txt']
        ];
        yield 'content and base64' => [
            LogicException::class,
            'You should only provide contents or base64',
            ['originalFilename' => 'tmp.txt', 'contents' => '', 'base64' => '']
        ];
        yield 'invalid file name' => [
            ValidationException::class,
            'Validation error:  Value "path/tmp.txt" is not valid for value object of type: Filename',
            ['contents' => 'hello', 'originalFilename' => 'path/tmp.txt']
        ];
        yield 'invalid base64 contents' => [
            ValidationException::class,
            'Validation error:  Value "hello" is not valid for value object of type: Base64Stream',
            ['base64' => 'hello', 'originalFilename' => 'tmp.txt']
        ];
    }

    /**
     * @test
     */
    public function it_works_with_schema_generator()
    {
        if (!class_exists(Reference::class)) {
            $this->markTestSkipped('cebe/openapi-php is missing');
        }
        $this->runOpenapiSchemaTestForCreation(
            JsonFileUpload::class,
            'JsonFileUpload-post',
            [
                'required' => [
                    'originalFilename',
                ],
                'oneOf' => [
                    [
                        'required' => ['contents', 'originalFilename'],
                    ],
                    [
                        'required' => ['base64', 'originalFilename'],
                    ],
                ],
                'type' => 'object',
                'required' => ['originalFilename'],
                'properties' => [
                    'originalFilename' => new Reference(['$ref' => '#/components/schemas/Filename-post']),
                    'mime' => new Reference(['$ref' => '#/components/schemas/StrictMimeType-nullable-post']),
                    'contents' => new Reference(['$ref' => '#/components/schemas/BinaryStream-post']),
                    'base64' => new Reference(['$ref' => '#/components/schemas/Base64Stream-post']),
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(JsonFileUpload::class);
    }
}
