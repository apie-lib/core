<?php
namespace Apie\Tests\Core\FileStorage;

use Apie\Core\FileStorage\ChainedFileStorage;
use Apie\Core\FileStorage\InlineStorage;
use Apie\Core\FileStorage\StoredFile;
use Apie\Core\FileStorage\TextFile;
use Nyholm\Psr7\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileTest extends TestCase
{
    #[Test]
    #[DataProvider('indexProvider')]
    public function it_can_read_indexes_properly(array $expectedIndex, StoredFile $file)
    {
        $this->assertEquals($expectedIndex, $file->getIndexing());
        $this->assertEquals($expectedIndex, $file->getIndexing(), 'repeat reads work (or are cached)');
    }

    #[Test]
    #[DataProvider('contentProvider')]
    public function it_can_read_file_content_properly(string $expectedContents, StoredFile $file)
    {
        $this->assertEquals($expectedContents, $file->getContent());
        $this->assertEquals($expectedContents, $file->getContent(), 'repeat reads work (or are cached)');
    }

    #[Test]
    #[DataProvider('serverMimeProvider')]
    public function it_can_read_server_mimetype_properly(string $expectedContents, StoredFile $file)
    {
        $actual = $file->getServerMimeType();
        $this->assertEquals($expectedContents, $actual);
        $this->assertEquals($expectedContents, $file->getServerMimeType(), 'repeat reads work (or are cached)');
    }

    #[Test]
    #[DataProvider('errorProvider')]
    public function it_can_read_error_status_properly(int $expectedError, StoredFile $file)
    {
        $this->assertEquals($expectedError, $file->getError());
        $this->assertEquals($expectedError, $file->getError(), 'repeat reads work (or are cached)');
    }

    #[Test]
    #[DataProvider('sizeProvider')]
    public function it_can_read_file_size_properly(int $expectedSize, StoredFile $file)
    {
        $actualSize = $file->getSize();

        $this->assertEquals($expectedSize, $actualSize);
        $this->assertEquals($expectedSize, $file->getSize(), 'repeat reads work (or are cached)');
    }
    
    private static function provideFileVariations(): \Generator
    {
        $fixturePath =  __DIR__ . '/../../fixtures/LocalFileStorage/example.txt';
        $uploadedFile = new UploadedFile(
           $fixturePath,
           filesize($fixturePath),
           UPLOAD_ERR_OK,
           'example.txt',
           'plain/text'
        );
        $fileStorage = new ChainedFileStorage([new InlineStorage()], [new InlineStorage()]);
        $storagePath = $fileStorage->psrToPath($uploadedFile);

        yield 'inline string' => StoredFile::createFromString(
            file_get_contents($fixturePath),
            'plain/text',
            'example.txt'
        );
        yield 'local file' => StoredFile::createFromLocalFile($fixturePath, 'plain/text');
        yield 'from storage path' => StoredFile::createFromStorage($fileStorage, $storagePath);
        yield 'from PSR uploaded file instance (no StoredFile)' => StoredFile::createFromUploadedFile($uploadedFile);
        yield 'from PSR uploaded file instance (StoredFile child class)' => TextFile::createFromUploadedFile($uploadedFile);
        yield 'from resource' => StoredFile::createFromResource(
            StoredFile::createFromUploadedFile($uploadedFile)->getStream()->detach(),
            'plain/text',
            'example.txt'
        );
        // TODO fromDto?
    }
    public static function indexProvider(): \Generator
    {
        $expectedIndex = [
            'lorem' => 1,
            'ipsum' => 1,
        ];
        foreach (self::provideFileVariations() as $description => $file) {
            yield $description => [$expectedIndex, $file];
        }
    }

    public static function contentProvider(): \Generator
    {
        $expectedIndex = 'Lorem ipsum';
        foreach (self::provideFileVariations() as $description => $file) {
            yield $description => [$expectedIndex, $file];
        }
    }

    public static function serverMimeProvider(): \Generator
    {
        $expectedIndex = 'text/plain';
        foreach (self::provideFileVariations() as $description => $file) {
            yield $description => [$expectedIndex, $file];
        }
    }

    
    public static function errorProvider(): \Generator
    {
        $expectedIndex = UPLOAD_ERR_OK;
        foreach (self::provideFileVariations() as $description => $file) {
            yield $description => [$expectedIndex, $file];
        }
    }

    public static function sizeProvider(): \Generator
    {
        $expectedIndex = strlen('Lorem ipsum');
        foreach (self::provideFileVariations() as $description => $file) {
            yield $description => [$expectedIndex, $file];
        }
    }
}