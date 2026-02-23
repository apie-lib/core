<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\FileStorage\StoredFile;
use Faker\Generator;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;
use ReflectionProperty;

/**
 * @phpstan-ignore apie.conflicting.interface
 */
#[Description('URL to download file')]
#[FakeMethod('createRandom')]
#[SchemaMethod('createSchema')]
class FileUri extends Uri implements UploadedFileInterface
{
    private StoredFile $loadedFile;

    final protected function toFile(): UploadedFileInterface
    {
        if (!isset($this->loadedFile)) {
            $contentType = null;
            if (class_exists('GuzzleHttp\Client')) {
                $client = new \GuzzleHttp\Client();
                try {
                    $response = $client->request('HEAD', $this->toNative());
                    $contentType = $response->getHeaderLine('Content-Type');
                    // Use $contentType as needed
                } catch (\Exception $e) {
                    // Fallback to existing logic
                }
            }
            $stream = fopen($this->toNative(), 'rb');
            $this->loadedFile = StoredFile::createFromResource($stream, clientMimeType: $contentType);
        }
        
        return $this->loadedFile;
    }

    public function getStream(): StreamInterface
    {
        return $this->toFile()->getStream();
    }

    public function moveTo($targetPath): void
    {
        $this->toFile()->moveTo($targetPath);
    }

    public function getSize(): ?int
    {
        return $this->toFile()->getSize();
    }

    public function getError(): int
    {
        return $this->toFile()->getError();
    }

    public function getClientFilename(): ?string
    {
        return $this->toFile()->getClientFilename();
    }

    public function getClientMediaType(): ?string
    {
        return $this->toFile()->getClientMediaType();
    }

    public static function createRandom(Generator $faker): Uri
    {
        $refl = new ReflectionClass(static::class);
        $instance = $refl->newInstanceWithoutConstructor();
        $prop = new ReflectionProperty(Uri::class, 'internal');
        $prop->setValue($instance, $faker->url());
        $instance->loadedFile = $faker->fakeClass(StoredFile::class);
        return $instance;
    }

    /**
     * @return array<string, string|null>
     */
    public static function createSchema(): array
    {
        $attr = new ReflectionClass(static::class);
        $description = null;
        foreach ($attr->getAttributes(Description::class) as $descriptionAttr) {
            $descriptionInstance = $descriptionAttr->newInstance();
            $description = $descriptionInstance->description;
        }
        return [
            'type' => 'string',
            'format' => 'fileuri',
            'description' => $description,
        ];
    }
}
