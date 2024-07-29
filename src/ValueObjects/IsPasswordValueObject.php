<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Randomizer\RandomizerInterface;
use Apie\Core\Randomizer\SecureRandomizer;
use SensitiveParameter;
use Stringable;

trait IsPasswordValueObject
{
    use IsStringWithRegexValueObject {
        __construct as private initObject;
    }

    public function __construct(#[SensitiveParameter] string|int|float|bool|Stringable $input)
    {
        $this->initObject($input);
    }

    public static function getRegularExpression(): string
    {
        $lowercase = '(?=(.*[a-z]){' . self::getMinLowercase() . ',})';
        $uppercase = '(?=(.*[A-Z]){' . self::getMinUppercase() . ',})';
        $digits = '(?=(.*[0-9]){' . self::getMinDigits() . ',})';
        $specialCharactersRegex = str_replace('\#', '#', preg_quote(self::getAllowedSpecialCharacters(), '/'));
        $specialCharacter = '(?=(.*[' . $specialCharactersRegex . ']){' . self::getMinSpecialCharacters() . ',})';
        $totalSize = '[a-zA-Z0-9' . $specialCharactersRegex . ']{' . self::getMinLength() . ',' . self::getMaxLength() . '}';
        return '/^' . $lowercase . $uppercase . $digits . $specialCharacter . $totalSize . '$/';
    }

    abstract public static function getMinLength(): int;

    abstract public static function getMaxLength(): int;

    abstract public static function getAllowedSpecialCharacters(): string;

    abstract public static function getMinSpecialCharacters(): int;

    abstract public static function getMinDigits(): int;

    abstract public static function getMinLowercase(): int;

    abstract public static function getMinUppercase(): int;

    public static function createRandom(RandomizerInterface $generator = new SecureRandomizer()): static
    {
        $minLength = self::getMinLength();
        $maxLength = self::getMaxLength();
        $minSpecialCharacters = self::getMinSpecialCharacters();
        $minDigits = self::getMinDigits();
        $minLowercase = self::getMinLowercase();
        $minUppercase = self::getMinUppercase();
        $lowercaseCharacters = str_split('abcdefghijklmnopqrstuvwxyz');
        $uppercaseCharacters = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $specialCharacters = str_split(self::getAllowedSpecialCharacters());
        $generatedPassword = $generator->randomElements($specialCharacters, $minSpecialCharacters);
        for ($i = 0; $i < $minDigits; $i++) {
            $generatedPassword[] = $generator->randomDigit();
        }
        for ($i = 0; $i < $minLowercase; $i++) {
            $generatedPassword[] = $generator->randomElement($lowercaseCharacters);
        }
        for ($i = 0; $i < $minUppercase; $i++) {
            $generatedPassword[] = $generator->randomElement($uppercaseCharacters);
        }
        $length = $generator->numberBetween($minLength, $maxLength);
        for ($i = count($generatedPassword); $i < $length; $i++) {
            $generatedPassword[] = $generator->randomElement([
                ...$lowercaseCharacters,
                ...$uppercaseCharacters,
                ...$specialCharacters
            ]);
        }
        if (count($generatedPassword) > $maxLength) {
            $generatedPassword = array_slice($generatedPassword, 0, $maxLength);
        }
        $generator->shuffle($generatedPassword);

        return self::fromNative(implode('', $generatedPassword));
    }
}
