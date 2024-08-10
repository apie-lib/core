<?php
namespace Apie\Tests\Core\FileStorage;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\FileStorage\ImageFile;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use Apie\Serializer\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class ImageFileTest extends TestCase
{
    use TestWithFaker;

    /**
     * @test
     */
    public function it_works_with_images()
    {
        $testItem = ImageFile::createFromString(
            '<svg></svg>',
            'image/svg',
            'test.svg'
        );
        $this->assertEquals('<svg></svg>', $testItem->getContent());
        $this->assertEquals('image/svg', $testItem->getClientMediaType());
        $this->assertEquals('image/svg+xml', $testItem->getServerMimeType());
    }

    /**
     * @test
     */
    public function it_refuses_files_that_are_not_images()
    {
        if (!class_exists(ValidationException::class)) {
            $this->markTestSkipped('apie/serializer is missing');
        }
        $this->expectException(
            InvalidTypeException::class
        );
        ImageFile::createFromString(
            'Hello!',
            'image/svg',
            'test.txt',
        );
    }

    /**
     * @test
     */
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(ImageFile::class);
    }
}
