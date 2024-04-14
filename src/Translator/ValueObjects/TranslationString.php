<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use ReflectionClass;

final class TranslationString implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-z0-9_]+(\.[a-z0-9_]+)*$/i';
    }

    public function toPath(string $rootPath): string
    {
        return rtrim($rootPath, '/') . '/' . str_replace('.', '/', $this->internal);
    }

    public function toUnbounded(): self
    {
        if (str_starts_with('apie.bounded.', $this->internal)) {
            return new self(preg_replace('/^apie\.bounded\.[a-z0-9_]+\./i', 'apie.', $this->internal));
        }
        return $this;
    }

    public static function createForResourceName(ReflectionClass $class, ?BoundedContextId $boundedContextId = null): self
    {
        if ($boundedContextId === null) {
            return new self('apie.resource.' . SnakeCaseSlug::fromClass($class) . '.singular');
        }
        return new self('apie.bounded.' .  $boundedContextId . '.resource.singular');
    }
}
