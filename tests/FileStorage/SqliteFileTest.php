<?php

namespace Apie\Tests\Core\FileStorage;

use Apie\Core\FileStorage\SqliteFile;
use Apie\Fixtures\TestHelpers\ObjectTestCase;
use PHPUnit\Framework\Attributes\Test;

class SqliteFileTest extends ObjectTestCase
{
    public static function className(): string
    {
        return SqliteFile::class;
    }

    private function createExistingSqliteFile(): SqliteFile
    {
        return SqliteFile::createFromLocalFile(
            __DIR__ . '/../../fixtures/chinook.db',
            removeOnDestruct: false
        );
    }

    public static function getOpenApiSchemaForCreation(): array
    {
        return [
            'required' => ['originalFilename'],
            'type' => 'object',
            'oneOf' => [
                [
                    'required' => [
                        'contents',
                        'originalFilename',
                    ],
                ],
                [
                    'required' => [
                        'base64',
                        'originalFilename',
                    ],
                ]
            ],
            'properties' => [
                'contents' => [
                    '$ref' => '#/components/schemas/BinaryStream-post'
                ],
                'originalFilename' => [
                    '$ref' => '#/components/schemas/Filename-post'
                ],
                'base64' => [
                    '$ref' => '#/components/schemas/Base64Stream-post'
                ],
                'mime' => [
                    '$ref'=> '#/components/schemas/StrictMimeType-nullable-post'
                ]
            ],
        ];
    }

    #[Test]
    public function cloning_the_uploaded_file_will_deselect_the_server_path()
    {
        $testItem = $this->createExistingSqliteFile();
        $this->assertNotNull($testItem->getServerPath());
        $clone = clone $testItem;
        $this->assertEquals($testItem->getServerPath(), $clone->getServerPath());
        $serverPath = $clone->getOrSetOnServerPath();
        $this->assertEquals($serverPath, $testItem->getServerPath());
    }

    #[Test]
    public function i_can_query_a_db_table_from_existing_file()
    {
        $testItem = $this->createExistingSqliteFile();
        $actual = $testItem->fetchAll('SELECT CustomerId, FirstName, LastName 
FROM Customer
ORDER BY CustomerId DESC
LIMIT 4');
        $expected = [
            [
                'CustomerId' => 59,
                'FirstName' => 'Puja',
                'LastName' => 'Srivastava',
            ],
            [
                'CustomerId' => 58,
                'FirstName' => 'Manoj',
                'LastName' => 'Pareek',
            ],
            [
                'CustomerId' => 57,
                'FirstName' => 'Luis',
                'LastName' => 'Rojas',
            ],
            [
                'CustomerId' => 56,
                'FirstName' => 'Diego',
                'LastName' => 'Gutiérrez',
            ],
        ];
        $this->assertEquals($expected, $actual);
    }
}
