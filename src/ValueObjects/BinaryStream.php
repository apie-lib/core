<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;

#[SchemaMethod("getSchema")]
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
