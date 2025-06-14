<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\Optional;
use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\FileStorage\StoredFile;
use Apie\SchemaGenerator\Builders\ComponentsBuilder;
use Apie\SchemaGenerator\Enums\SchemaUsages;

#[SchemaMethod('createSchema')]
#[Description('Uploads a file with JSON.')]
final class JsonFileUpload implements CompositeWithOwnValidation
{
    use CompositeValueObject;

    public function __construct(
        private Filename $originalFilename,
        #[Optional]
        private BinaryStream $contents,
        #[Optional]
        private Base64Stream $base64,
        #[Optional]
        private ?StrictMimeType $mime = null
    ) {
    }

    protected function validateState(): void
    {
        if (isset($this->contents) xor isset($this->base64)) {
            return;
        }
        throw new \LogicException(
            isset($this->contents)
            ? 'You should only provide contents or base64'
            : 'I need either a "contents" or a "base64" property'
        );
    }

    /**
     * @template T of StoredFile
     * @param class-string<T> $className
     * @return T
     */
    public function toUploadedFile(string $className = StoredFile::class): StoredFile
    {
        $contents = isset($this->contents) ? $this->contents->toNative() : $this->base64->decode()->toNative();
        return $className::createFromString(
            $contents,
            isset($this->mime) ? $this->mime->toNative() : null,
            $this->originalFilename->toNative(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function createSchema(SchemaUsages $schemaUsage, ComponentsBuilder $componentsBuilder): array
    {
        return [
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
            'properties' => [
                'contents' => $schemaUsage->toSchema($componentsBuilder, BinaryStream::class),
                'originalFilename' => $schemaUsage->toSchema($componentsBuilder, Filename::class),
                'base64' => $schemaUsage->toSchema($componentsBuilder, Base64Stream::class),
                'mime' => $schemaUsage->toSchema($componentsBuilder, StrictMimeType::class, nullable: true),
            ]
        ];
    }
}
