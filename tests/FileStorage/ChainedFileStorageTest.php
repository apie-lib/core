<?php
namespace Apie\Tests\Core\FileStorage;

use Apie\Core\FileStorage\ChainedFileStorage;
use Apie\Core\FileStorage\InlineStorage;
use PHPUnit\Framework\TestCase;

class ChainedFileStorageTest extends TestCase
{
    private function createTestItem(): ChainedFileStorage
    {
        $inlineStorage = new InlineStorage();
        return new ChainedFileStorage(
            [$inlineStorage],
            [$inlineStorage]
        );
    }

    /**
     * @test
     */
    public function it_can_save_resources()
    {
        $testItem = $this->createTestItem();
        $resource = fopen(__FILE__, 'r');
        try {
            $actual = $testItem->resourceToPath($resource);
            $this->assertEquals(base64_encode(file_get_contents(__FILE__)), $actual);
        } finally {
            fclose($resource);
        }
    }

    /**
     * @test
     */
    public function it_can_restore_resources()
    {
        $testItem = $this->createTestItem();
        $actual = $testItem->pathToResource(base64_encode('Hello'));
        $this->assertEquals('Hello', stream_get_contents($actual));
    }
}
