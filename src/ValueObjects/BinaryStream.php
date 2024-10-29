<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\Dto\CmsInputOption;
use Apie\Core\Enums\FileStreamType;
use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;

#[SchemaMethod("getSchema")]
#[CmsSingleInput(['stream', 'file'], new CmsInputOption(streamType: FileStreamType::BinaryString))]
final class BinaryStream implements StringValueObjectInterface
{
    use IsStringValueObject;

    /**
     * @return array<string, string>
     */
    public static function getSchema(): array
    {
        return [
            'type' => 'string',
            'format' => 'binary'
        ];
    }
}
