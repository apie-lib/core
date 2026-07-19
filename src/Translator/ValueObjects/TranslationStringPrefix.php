<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;

#[Description('prefix of a Apie translation string. It includes the trailing dot!')]
#[ExampleValue('apie.bounded.test.')]
#[ExampleValue('apie.bounded.test.resource.user.')]
#[ExampleValue('apie.')]
#[ExampleValue('apie.resource.user.')]
final class TranslationStringPrefix implements HasRegexValueObjectInterface
{
    public function __construct(
        private ?BoundedContextId $boundedContextId = null,
        private ?SnakeCaseSlug $resourceIdentifier = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->toNative();
    }

    public function jsonSerialize(): mixed
    {
        return $this->toNative();
    }

    public static function getRegularExpression(): string
    {
        return '/^apie\.(bounded\.([a-z][a-z0-9]*)\.)?(resource\.([a-z0-9]+(_[a-z0-9]+)*)\.)?$/';
    }

    public function toNative(): string
    {
        $resourceSection = $this->resourceIdentifier ? ('resource.' . $this->resourceIdentifier . '.') : '';
        if ($this->boundedContextId === null) {
            return 'apie.' . $resourceSection;
        }
        return 'apie.bounded.' .  $this->boundedContextId . '.' . $resourceSection;
    }

    public static function fromNative(mixed $input): ValueObjectInterface
    {
        $input = Utils::toString($input);
        if (preg_match(
            '/^apie\.(bounded\.(?<bounded>[a-z][a-z0-9]*)\.)?(resource\.(?<resource>[a-z0-9]+(_[a-z0-9]+)*)\.)?$/',
            $input,
            $matches
        )) {
            return new TranslationStringPrefix(
                empty($matches['bounded']) ? null : BoundedContextId::fromNative($matches['bounded']),
                empty($matches['resource']) ? null : SnakeCaseSlug::fromNative($matches['resource'])
            );
        }
        throw new InvalidStringForValueObjectException($input, new \ReflectionClass(static::class));
    }

    public static function createFromTranslation(string $translationString): TranslationStringPrefix
    {
        if (preg_match(
            '/^apie\.(bounded\.(?<bounded>[a-z][a-z0-9]*)\.)?(resource\.(?<resource>[a-z0-9]+(_[a-z0-9]+)*)\.)?/',
            $translationString,
            $matches
        )) {
            return new TranslationStringPrefix(
                empty($matches['bounded']) ? null : BoundedContextId::fromNative($matches['bounded']),
                empty($matches['resource']) ? null : SnakeCaseSlug::fromNative($matches['resource'])
            );
        }
        throw new InvalidStringForValueObjectException($translationString, new \ReflectionClass(static::class));
    }

}
