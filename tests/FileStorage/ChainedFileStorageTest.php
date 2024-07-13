<?php
namespace Apie\Tests\Core\FileStorage;

use Apie\Core\FileStorage\ChainedFileStorage;
use Apie\Core\FileStorage\InlineStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ChainedFileStorageTest extends TestCase
{
    private function createTestItem(): ChainedFileStorage
    {
        $inlineStorage = new InlineStorage();
        return new ChainedFileStorage(
            [$inlineStorage],
            [$inlineStorage],
            [$inlineStorage],
        );
    }

    /**
     * @test
     */
    public function it_can_load_uploaded_file_interface()
    {
        $testItem = $this->createTestItem();
        $actual = $testItem->pathToUploadedFile(
            'application/json|original.txt|' . base64_encode('This is a text|This is a text')
        );
        try {
            $this->assertEquals('original.txt', $actual->getClientOriginalName());
            $this->assertEquals('application/json', $actual->getClientMimeType());
            $this->assertEquals('This is a text|This is a text', $actual->getContent());
            $this->assertTrue(file_exists($actual->getPathname()));
        } finally {
            unlink($actual->getPathname());
        }
    }

    /**
     * @test
     */
    public function it_can_save_uploaded_file_interface()
    {
        $testItem = $this->createTestItem();
        $tempFile = tempnam(sys_get_temp_dir(), 'storageTest') . '.php';
        try {
            $contents = file_get_contents(__FILE__);
            file_put_contents($tempFile, $contents);
            $uploadedFile = new UploadedFile($tempFile, 'ChainedFileStorageTest.php', test: true);
            $actual = $testItem->uploadedFileToPath($uploadedFile);
            $this->assertEquals('text/x-php|ChainedFileStorageTest.php|' . base64_encode($contents), $actual);
        } finally {
            @unlink($tempFile);
        }
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
