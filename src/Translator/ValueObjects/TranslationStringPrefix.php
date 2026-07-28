<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Translator\Lists\TranslationStringPrefixSet;
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
    public static function fromApieContext(ApieContext $context): static
    {
        $resourceIdentifier = $context->getContext(ContextConstants::RESOURCE_NAME, false);
        if (!$resourceIdentifier && $context->hasContext(ContextConstants::SERVICE_CLASS)) {
            $resourceIdentifier = $context->getContext(ContextConstants::SERVICE_CLASS, false);
        }
        if ($resourceIdentifier) {
            $resourceIdentifier = SnakeCaseSlug::fromClass(new \ReflectionClass($resourceIdentifier));
        }
        return new static(
            $context->getContext(BoundedContextId::class, false),
            $resourceIdentifier
        );
    }
    public function getBoundedContextId(): ?BoundedContextId
    {
        return $this->boundedContextId;
    }

    public function getResourceIdentifier(): ?SnakeCaseSlug
    {
        return $this->resourceIdentifier;
    }

    public function getSimplifications(): TranslationStringPrefixSet
    {
        $list = [];
        if ($this->boundedContextId !== null) {
            $list[] = new static(null, $this->resourceIdentifier);
            if ($this->resourceIdentifier !== null) {
                $list[] = new static($this->boundedContextId, null);
            }
        } elseif ($this->resourceIdentifier !== null) {
            $list[] = new static();
        }
        return new TranslationStringPrefixSet($list);
    }

    final public function getSpecifity(): int
    {
        return ($this->boundedContextId ? 2 : 0) + ($this->resourceIdentifier ? 4 : 0);
    }

    public function withoutBoundedContextId(): static
    {
        if ($this->boundedContextId === null) {
            return $this;
        }

        return new TranslationStringPrefix(null, $this->resourceIdentifier);
    }

    public function withBoundedContextId(BoundedContextId $boundedContextId): static
    {
        if ($this->boundedContextId?->toNative() === $boundedContextId->toNative()) {
            return $this;
        }

        return new TranslationStringPrefix($boundedContextId, $this->resourceIdentifier);
    }

    public function withoutResourceIdentifier(): static
    {
        if ($this->resourceIdentifier === null) {
            return $this;
        }

        return new TranslationStringPrefix($this->boundedContextId, null);
    }

    public function withResourceIdentifier(SnakeCaseSlug $resourceIdentifier): static
    {
        if ($this->resourceIdentifier?->toNative() === $resourceIdentifier->toNative()) {
            return $this;
        }

        return new TranslationStringPrefix($this->boundedContextId, $resourceIdentifier);
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
