<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\CmsSingleInput;
use Apie\Core\Attributes\CmsValidationCheck;
use Apie\Core\Attributes\Description;
use Apie\Core\Randomizer\RandomizerInterface;
use Apie\Core\Randomizer\SecureRandomizer;
use SensitiveParameter;
use Stringable;

#[Description('Represents a password. The password has certain restrictions like a minimum amount of alpha-numeric, digits and special characters')]
#[CmsSingleInput(['password'])]
#[CmsValidationCheck(message: 'apie.validation_errors.length', minLengthMethod: 'getMinLength', maxLengthMethod: 'getMaxLength')]
#[CmsValidationCheck(message: 'apie.validation_errors.password.lower_case', patternMethod: 'getLowercaseRegularExpression')]
#[CmsValidationCheck(message: 'apie.validation_errors.password.upper_case', patternMethod: 'getUppercaseRegularExpression')]
#[CmsValidationCheck(message: 'apie.validation_errors.password.digit', patternMethod: 'getDigitRegularExpression')]
#[CmsValidationCheck(message: 'apie.validation_errors.password.special', patternMethod: 'getSpecialCharactersRegularExpression')]
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
        $lowercase = self::createRegexPart('[a-z]', self::getMinLowercase());
        $uppercase = self::createRegexPart('[A-Z]', self::getMinUppercase());
        $digits = self::createRegexPart('[0-9]', self::getMinDigits());
        $specialCharactersRegex = str_replace('\#', '#', preg_quote(self::getAllowedSpecialCharacters(), '/'));
        $specialCharacter = self::createRegexPart('[' . $specialCharactersRegex . ']', self::getMinSpecialCharacters());
        
        $totalSize = '[a-zA-Z0-9' . $specialCharactersRegex . ']{' . self::getMinLength() . ',' . self::getMaxLength() . '}';
        return '/^' . $lowercase . $uppercase . $digits . $specialCharacter . $totalSize . '$/';
    }

    private static function createRegexPart(string $expression, ?int $minCount = null): string
    {
        return '(?=(.*'
            . $expression
            . '){'
            . ($minCount === null ? '' : $minCount)
            . ',})';
    }

    public static function getLowercaseRegularExpression(): string
    {
        return '/^' . self::createRegexPart('[a-z]', self::getMinLowercase()) . '.*$/';
    }

    public static function getUppercaseRegularExpression(): string
    {
        return '/^' . self::createRegexPart('[A-Z]', self::getMinUppercase()) . '.*$/';
    }

    public static function getDigitRegularExpression(): string
    {
        return '/^' . self::createRegexPart('[0-9]', self::getMinDigits()) . '.*$/';
    }

    public static function getSpecialCharactersRegularExpression(): ?string
    {
        $allowedSpecial = self::getAllowedSpecialCharacters();
        if (empty($allowedSpecial)) {
            return null;
        }
        $specialCharactersRegex = str_replace('\#', '#', preg_quote($allowedSpecial, '/'));
        return '/^' . self::createRegexPart(
            '[' . $specialCharactersRegex . ']',
            self::getMinSpecialCharacters()
        ) . '.*$/';
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
