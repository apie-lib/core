<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Attributes\SchemaMethod;
use Apie\Core\RegexUtils;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Faker\Generator;

#[SchemaMethod("getSchema")]
#[FakeMethod('createRandom')]
final class Base64Stream implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '#^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=)?$#';
    }

    protected function convert(string $input): string
    {
        return preg_replace('/\s/s', '', $input);
    }

    public static function createRandom(Generator $generator): self
    {
        return new self(base64_encode($generator->text()));
    }

    /**
     * @return array<string, string>
     */
    public static function getSchema(): array
    {
        return [
            'type' => 'string',
            'format' => 'base64',
            'pattern' => RegexUtils::removeDelimiters(self::getRegularExpression()),
        ];
    }
}
