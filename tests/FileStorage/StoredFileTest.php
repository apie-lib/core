<?php
namespace Apie\Tests\Core\FileStorage;

use Apie\Core\Enums\UploadedFileStatus;
use Apie\Core\FileStorage\ChainedFileStorage;
use Apie\Core\FileStorage\ImageFile;
use Apie\Core\FileStorage\InlineStorage;
use Apie\Core\FileStorage\StoredFile;
use LogicException;
use PHPUnit\Framework\TestCase;

class StoredFileTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_work_from_a_string()
    {
        $testItem = StoredFile::createFromString('This is a test', 'incorrect/mime', 'dummy.txt');
        $this->assertEquals(UploadedFileStatus::CreatedLocally, $testItem->getStatus());
        $this->assertEquals('This is a test', $testItem->getContent());
        $this->assertEquals('This is a test', $testItem->getStream()->__toString());
        $this->assertEquals('This is a test', $testItem->getStream()->__toString(), 'It can be called multiple times');
        $this->assertEquals(14, $testItem->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $testItem->getError());
        $this->assertEquals('dummy.txt', $testItem->getClientFilename());
        $this->assertEquals('incorrect/mime', $testItem->getClientMediaType());
        $this->assertEquals('text/plain', $testItem->getServerMimeType());
        $this->assertNull($testItem->getServerPath());

        $tempPath = tempnam(sys_get_temp_dir(), '');
        try {
            $testItem->moveTo($tempPath);
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage('File is already moved to ' . $tempPath);
            $testItem->moveTo($tempPath);
        } finally {
            @unlink(@$tempPath);
        }
    }

    /**
     * @test
     */
    public function it_can_work_from_a_static_file()
    {
        $testItem = StoredFile::createFromLocalFile(__FILE__, clientMimeType: 'incorrect/mime', removeOnDestruct: false);
        $this->assertEquals(UploadedFileStatus::CreatedLocally, $testItem->getStatus());
        $expectedContents = file_get_contents(__FILE__);
        $this->assertEquals($expectedContents, $testItem->getContent());
        $this->assertEquals($expectedContents, $testItem->getStream()->__toString());
        $this->assertEquals($expectedContents, $testItem->getStream()->__toString(), 'It can be called multiple times');
        $this->assertEquals(filesize(__FILE__), $testItem->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $testItem->getError());
        $this->assertEquals('StoredFileTest.php', $testItem->getClientFilename());
        $this->assertEquals('incorrect/mime', $testItem->getClientMediaType());
        $this->assertEquals('text/x-php', $testItem->getServerMimeType());
        $this->assertEquals(__FILE__, $testItem->getServerPath());

        $tempPath = tempnam(sys_get_temp_dir(), '');
        try {
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage(__FILE__ . ' is not a temporary file');
            $testItem->moveTo($tempPath);
        } finally {
            // to avoid accidentally throwing away a test.
            if (file_exists($tempPath) && !file_exists(__FILE__)) {
                rename($tempPath, __FILE__);
            }
            @unlink(@$tempPath);
        }
    }

    /**
     * @test
     */
    public function it_can_work_from_a_file_from_storage()
    {
        $expectedContents = '<html></html>';
        $testItem = StoredFile::createFromStorage(
            new ChainedFileStorage([new InlineStorage()],[], []),
            'text/html|test.html|' . base64_encode($expectedContents)
        );

        $this->assertEquals(UploadedFileStatus::StoredInStorage, $testItem->getStatus());
        
        $this->assertEquals($expectedContents, $testItem->getContent());
        $this->assertEquals($expectedContents, $testItem->getStream()->__toString());
        $this->assertEquals($expectedContents, $testItem->getStream()->__toString(), 'It can be called multiple times');
        $this->assertEquals(13, $testItem->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $testItem->getError());
        $this->assertEquals('test.html', $testItem->getClientFilename());
        $this->assertEquals('text/html', $testItem->getClientMediaType());
        $this->assertEquals('text/html', $testItem->getServerMimeType());
        $this->assertEquals(null, $testItem->getServerPath());

        $tempPath = tempnam(sys_get_temp_dir(), '');
        try {
            $testItem->moveTo($tempPath);
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage('File is already moved to ' . $tempPath);
            $testItem->moveTo($tempPath);
        } finally {
            @unlink(@$tempPath);
        }
    }

    /**
     * @test
     */
    public function it_will_not_wrap_uploaded_files_if_not_needed()
    {
        $internal = StoredFile::createFromString(
            'This is a test',
            'incorrect/mime',
            'dummy.txt',
        );
        $this->assertSame($internal, StoredFile::createFromUploadedFile($internal));
    }

    /**
     * @test
     */
    public function it_can_wrap_other_instances_of_uploaded_files()
    {
        $pngFilePath = __DIR__ . '/../../fixtures/apie-logo.png';
        $expectedContents = file_get_contents($pngFilePath);
        $testItem = StoredFile::createFromUploadedFile(ImageFile::createFromLocalFile(
            $pngFilePath,
        ));
        $this->assertEquals(UploadedFileStatus::CreatedLocally, $testItem->getStatus());
        $this->assertEquals($expectedContents, $testItem->getContent());
        $this->assertEquals($expectedContents, $testItem->getStream()->__toString());
        $this->assertEquals($expectedContents, $testItem->getStream()->__toString(), 'It can be called multiple times');
        $this->assertEquals(filesize($pngFilePath), $testItem->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $testItem->getError());
        $this->assertEquals('apie-logo.png', $testItem->getClientFilename());
        $this->assertEquals(null, $testItem->getClientMediaType());
        $this->assertEquals('image/png', $testItem->getServerMimeType());
        $this->assertEquals($pngFilePath, $testItem->getServerPath());

        $tempPath = tempnam(sys_get_temp_dir(), '');
        try {
            $this->expectException(LogicException::class);
            $this->expectExceptionMessage($pngFilePath . ' is not a temporary file');
            $testItem->moveTo($tempPath);
        } finally {
            @unlink(@$tempPath);
        }
    }

    /**
     * @test
     */
    public function it_works_on_resources()
    {
        $resource = fopen(__FILE__, 'r');
        $testItem = StoredFile::createFromResource(
            $resource,
            clientMimeType: 'incorrect/mime'
        );
        try {
            $this->assertEquals(UploadedFileStatus::FromRequest, $testItem->getStatus());
            $expectedContents = file_get_contents(__FILE__);
            $this->assertEquals($expectedContents, $testItem->getContent());
            $this->assertEquals($expectedContents, $testItem->getStream()->__toString());
            $this->assertEquals($expectedContents, $testItem->getStream()->__toString(), 'It can be called multiple times');
            $this->assertEquals(filesize(__FILE__), $testItem->getSize());
            $this->assertEquals(UPLOAD_ERR_OK, $testItem->getError());
            $this->assertEquals(null, $testItem->getClientFilename());
            $this->assertEquals('incorrect/mime', $testItem->getClientMediaType());
            $this->assertEquals('text/x-php', $testItem->getServerMimeType());
            $this->assertEquals(null, $testItem->getServerPath());
        } finally {
            fclose($resource);
        }
    }

    /**
     * @test
     */
    public function it_works_partially_on_unavailable_file()
    {
        $notExisting = __DIR__ . '/not-existing.txt';
        $this->assertFalse(file_exists($notExisting));
        $testItem = StoredFile::createFromLocalFile($notExisting);
        $this->assertEquals(UploadedFileStatus::CreatedLocally, $testItem->getStatus());
        $this->assertEquals($notExisting, $testItem->getServerPath());
        $this->assertEquals(UPLOAD_ERR_NO_FILE, $testItem->getError());
        $this->assertEquals('not-existing.txt', $testItem->getClientFilename());
        $this->assertEquals(null, $testItem->getClientMediaType());
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Could not load content');
        $testItem->getContent();
    }
}