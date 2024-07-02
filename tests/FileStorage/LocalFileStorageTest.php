<?php
namespace Apie\Tests\Core\FileStorage;

use Apie\Core\FileStorage\LocalFileStorage;
use PHPUnit\Framework\TestCase;

class LocalFileStorageTest extends TestCase
{
    private array $cleaning = [];

    protected function tearDown(): void
    {
        foreach ($this->cleaning as $path) {
            system('rm -rf '. escapeshellarg($path));
        }
        $this->cleaning = [];
    }

    public function givenALocalFileStorage(): LocalFileStorage
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('localfile');
        mkdir($path, recursive: true);
        $this->cleaning[] = $path;
        $fixturesPath = __DIR__ . '/../../fixtures/LocalFileStorage';
        copy($fixturesPath . '/example.txt', $path . '/example.txt');
        copy($fixturesPath . '/wrong-image.png', $path . '/wrong-image.png');
        return new LocalFileStorage(['path' => $path]);
    }

    /**
     * @test
     */
    public function it_can_create_and_find_uploaded_file_with_symfony_uploaded_file()
    {
        $testItem = $this->givenALocalFileStorage();
        $uploadedFile = $testItem->pathToUploadedFile('example.txt');
        $this->assertEquals('Lorem ipsum', $uploadedFile->getContent());
        $this->assertSame('example.txt', $testItem->uploadedFileToPath($uploadedFile));
    }

    /**
     * @test
     */
    public function it_can_create_and_find_uploaded_file_with_psr_uploaded_file()
    {
        $testItem = $this->givenALocalFileStorage();
        $uploadedFile = $testItem->pathToPsr('wrong-image.png');
        $this->assertEquals('application/x-empty', $uploadedFile->getClientMediaType());
        $this->assertSame('wrong-image.png', $testItem->psrToPath($uploadedFile));
    }
}
