<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\ApieLib;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\RegexUtils;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use Faker\Generator as FakerGenerator;
use ReflectionClass;
use RegRev\RegRev;

#[Description('A translation string for an Apie translation following a rigid, predictable structure.')]
#[ExampleValue('apie.bounded.example.resource.user.example.test.singular.authenticated')]
#[FakeMethod('createRandom')]
abstract class AbstractTranslation implements HasRegexValueObjectInterface
{
    protected const PREFIX_REGEX = 'apie\.(bounded\.([a-z][a-z0-9]*)\.)?(resource\.([a-z0-9]+(_[a-z0-9]+)*)\.)?';
    /**
     * Override this to define the translation namespace.
     * Must not include prefix or suffix separators.
     */
    protected const MIDDLE_REGEX = '[^.]+(\.[^.]+)*';
    protected const SUFFIX_REGEX = '(\.(singular|plural))?(\.(authenticated|unauthenticated))?';

    final protected function __construct(
        protected TranslationStringPrefix $prefix,
        protected string $middleSection,
        protected TranslationStringSuffix $suffix,
        protected ItemHashmap $placeholderValue
    ) {
        $regex = RegexUtils::fromPlaceholderToRegularExpression(static::MIDDLE_REGEX, includeAsCaptureGroup: false);
        if (!preg_match($regex, $middleSection)) {
            throw new InvalidStringForValueObjectException(
                $prefix . $middleSection . $suffix,
                new ReflectionClass(static::class)
            );
        }
    }

    public function getPlaceholders(): ItemHashmap
    {
        return $this->placeholderValue;
    }

    public static function getRegularExpression(): string
    {
        $regex = RegexUtils::fromPlaceholderToRegularExpression(static::MIDDLE_REGEX, false, false);
        // the str_replace is a temporary fix because RegRev seems to read [^.] incorrectly.
        return str_replace(
            '[^.]+',
            '[a-zA-Z0-9]+',
            '/^' . self::PREFIX_REGEX . $regex . self::SUFFIX_REGEX . '$/'
        );
    }

    final public function toNative(): string
    {
        return $this->prefix . $this->middleSection . $this->suffix;
    }

    final public function __toString(): string
    {
        return $this->toNative();
    }

    final public function jsonSerialize(): string
    {
        return $this->toNative();
    }

    final public static function fromNative(mixed $input): static
    {
        $input = Utils::toString($input);
        $prefix = TranslationStringPrefix::createFromTranslation($input);
        $inputSection = substr($input, strlen($prefix->toNative()));
        $suffix = TranslationStringSuffix::createFromTranslation($inputSection);
    
        $middleSection = substr($inputSection, 0, strlen($inputSection) - strlen($suffix->toNative()));

        $regex = RegexUtils::fromPlaceholderToRegularExpression(static::MIDDLE_REGEX);
        preg_match($regex, $middleSection, $placeholders);
        // @phpstan-ignore-next-line nullCoalesce.variable
        $placeholders = array_filter($placeholders ?? [], fn ($k) => !is_numeric($k), ARRAY_FILTER_USE_KEY);
        return new static(
            $prefix,
            $middleSection,
            $suffix,
            (new ItemHashmap($placeholders))
        );
    }

    public static function createRandom(FakerGenerator $factory): static
    {
        if (static::class === AbstractTranslation::class) {
            $aliases = explode('|', ApieLib::getAlias(AbstractTranslation::class));
            $alias = $factory->randomElement($aliases);

            return $alias::createRandom($factory);
        }

        $regularExpressionWithDelimiter = static::getRegularExpression();
        $regex = RegexUtils::removeDelimiters($regularExpressionWithDelimiter);
        return static::fromNative(RegRev::generate($regex));
    }

    public function withoutBoundedContextId(): static
    {
        return new static(
            $this->prefix->withoutBoundedContextId(),
            $this->middleSection,
            $this->suffix,
            $this->placeholderValue
        );
    }

    public function withBoundedContextId(BoundedContextId $boundedContextId): static
    {
        return new static(
            $this->prefix->withBoundedContextId($boundedContextId),
            $this->middleSection,
            $this->suffix,
            $this->placeholderValue
        );
    }

    public function withoutResourceIdentifier(): static
    {
        return new static(
            $this->prefix->withoutResourceIdentifier(),
            $this->middleSection,
            $this->suffix,
            $this->placeholderValue
        );
    }

    public function withResourceIdentifier(SnakeCaseSlug $resourceIdentifier): static
    {
        return new static(
            $this->prefix->withResourceIdentifier($resourceIdentifier),
            $this->middleSection,
            $this->suffix,
            $this->placeholderValue
        );
    }

    final public function getSpecifity(): int
    {
        return $this->prefix->getSpecifity() + $this->suffix->getSpecifity();
    }

    final public function getSimplifications(): TranslationStringSet
    {
        $list = [];
        foreach ($this->prefix->getSimplifications() as $prefixSimplification) {
            $list[] = new static(
                $prefixSimplification,
                $this->middleSection,
                $this->suffix,
                $this->placeholderValue
            );
            foreach ($this->suffix->getSimplifications() as $suffixSimplification) {
                $list[] = new static(
                    $prefixSimplification,
                    $this->middleSection,
                    $suffixSimplification,
                    $this->placeholderValue
                );
            }
        }
        foreach ($this->suffix->getSimplifications() as $suffixSimplification) {
            $list[] = new static(
                $this->prefix,
                $this->middleSection,
                $suffixSimplification,
                $this->placeholderValue
            );
        }
        return new TranslationStringSet($list);
    }

    abstract public function getFallbackText(): string;
}
