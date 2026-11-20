<?php
namespace Apie\Tests\Core\FileStorage;

use Apie\Core\FileStorage\AudioFile;
use Apie\Fixtures\TestHelpers\ObjectTestCase;

class AudioFileTest extends ObjectTestCase
{
    public static function className(): string
    {
        return AudioFile::class;
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'contents' => [
                    '$ref' => '#/components/schemas/BinaryStream-post'
                ],
                'base64' => [
                    '$ref' => '#/components/schemas/Base64Stream-post'
                ],
                'mime' => [
                    '$ref' => '#/components/schemas/StrictMimeType-nullable-post'
                ],
                'originalFilename' => [
                    '$ref' => '#/components/schemas/Filename-post'
                ]
            ],
            'required' => ['originalFilename'],
            'oneOf' => [
                ['required' => ['contents', 'originalFilename']],
                ['required' => ['base64', 'originalFilename']]
            ]
        ];
    }
}
