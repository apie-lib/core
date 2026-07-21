<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Translator\Enums\Pluralization;
use Apie\Core\Translator\Lists\TranslationStringSuffixSet;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Utils;

#[Description('Contains translation string suffixes to indicate an alternate translation when not logged in or in singular or plural form. It contains the leading dot.')]
#[ExampleValue('.singular')]
#[ExampleValue('.singular.authenticated')]
#[ExampleValue('.singular.unauthenticated')]
#[ExampleValue('.plural')]
#[ExampleValue('.plural.authenticated')]
#[ExampleValue('.plural.unauthenticated')]
#[ExampleValue('.authenticated')]
#[ExampleValue('.unauthenticated')]
#[ExampleValue('')]
final class TranslationStringSuffix implements HasRegexValueObjectInterface
{
    public function __construct(
        private ?Pluralization $pluralization = null,
        private ?bool $authenticated = null,
    ) {
    }

    public function getSimplifications(): TranslationStringSuffixSet
    {
        $list = [];
        if ($this->pluralization !== null) {
            $list[] = new static(null, $this->authenticated);
            if ($this->authenticated !== null) {
                $list[] = new static($this->pluralization, null);
            }
        } elseif ($this->authenticated !== null) {
            $list[] = new static();
        }
        return new TranslationStringSuffixSet($list);
    }

    final public function getSpecifity(): int
    {
        return ($this->pluralization ? 5 : 0) + ($this->authenticated ? 3 : 0);
    }

    public function __toString(): string
    {
        return $this->toNative();
    }

    public function jsonSerialize(): mixed
    {
        return $this->toNative();
    }

    public function toNative(): string
    {
        $suffix = '';
        if ($this->pluralization) {
            $suffix .= '.' . $this->pluralization->value;
        }
        if ($this->authenticated !== null) {
            $suffix .= '.' . ($this->authenticated ? 'authenticated' : 'unauthenticated');
        }

        return $suffix;
    }

    public static function getRegularExpression(): string
    {
        return '/^(\.(singular|plural))?(\.(authenticated|unauthenticated))?$/';
    }

    public static function fromNative(mixed $input): TranslationStringSuffix
    {
        $input = Utils::toString($input);
        if (preg_match(
            '/^(\.(?<plural>singular|plural))?(\.(?<auth>authenticated|unauthenticated))?$/',
            $input,
            $matches
        )) {
            return new TranslationStringSuffix(
                empty($matches['plural']) ? null : Pluralization::from($matches['plural']),
                empty($matches['auth']) ? null : $matches['auth'] === 'authenticated'
            );
        }
        throw new InvalidStringForValueObjectException($input, new \ReflectionClass(static::class));
    }

    public static function createFromTranslation(string $translationString): TranslationStringSuffix
    {
        if (preg_match(
            '/(\.(?<plural>singular|plural))?(\.(?<auth>authenticated|unauthenticated))?$/',
            $translationString,
            $matches
        )) {
            return new TranslationStringSuffix(
                empty($matches['plural']) ? null : Pluralization::from($matches['plural']),
                empty($matches['auth']) ? null : $matches['auth'] === 'authenticated'
            );
        }
        throw new InvalidStringForValueObjectException($translationString, new \ReflectionClass(static::class));
    }

}
