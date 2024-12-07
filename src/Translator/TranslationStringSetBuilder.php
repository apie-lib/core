<?php
namespace Apie\Core\Translator;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Translator\Enums\TranslationStringOperationType;
use Apie\Core\Translator\Enums\TranslationStringType;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\TranslationString;
use ReflectionClass;
use ReflectionProperty;

final class TranslationStringSetBuilder
{
    private ?TranslationStringType $translationStringType = null;

    private ?ReflectionProperty $property = null;

    private ?TranslationStringOperationType $operationType = null;

    /**
     * @param ReflectionClass<object> $resourceName
     */
    private function __construct(
        private ReflectionClass $resourceName,
        private ?BoundedContextId $boundedContextId = null
    ) {
    }

    /**
     * @param ReflectionClass<object> $resourceName
     */
    public static function create(ReflectionClass $resourceName, ?BoundedContextId $boundedContextId = null): self
    {
        return new self($resourceName, $boundedContextId);
    }

    /**
     * @param array<int, string|array<int, string>> $segments
     * @return array<int, string>
     */
    private function buildFromSegments(array $segments): array
    {
        $first = array_shift($segments);
        if ($first === null) {
            return [];
        }
        if (is_string($first)) {
            $res = [];
            if (empty($segments)) {
                return [$first];
            }
            foreach ($this->buildFromSegments($segments) as $segment) {
                $res[] = $first . $segment;
            }
            return $res;
        }
        $res = [];
        if (empty($segments)) {
            return $first;
        }
        foreach ($this->buildFromSegments($segments) as $segment) {
            foreach ($first as $prefix) {
                $res[] = $prefix . $segment;
            }
        }
        return $res;
    }

    /**
     * @param array<int, array<int, string|array<int, string>>> $allSegments
     */
    private function build(array... $allSegments): TranslationStringSet
    {
        return new TranslationStringSet(
            array_map(
                function (string $value) {
                    return TranslationString::fromNative($value);
                },
                array_merge(
                    ...array_map(
                        function (array $segments) {
                            return $this->buildFromSegments($segments);
                        },
                        $allSegments
                    )
                )
            )
        );
    }

    public function makeSet(): TranslationStringSet
    {
        if ($this->translationStringType === null) {
            throw new \LogicException(
                'No operation type is set, please call singular(), plural(), withProperty() or withPlaceholder()'
            );
        }
        $segments = [
            'apie.',
            $this->boundedContextId ? ['bounded.' . $this->boundedContextId . '.', ''] : '',
            'resource.',
            SnakeCaseSlug::fromClass($this->resourceName)->toNative(),
            '.',
            $this->operationType ? $this->operationType->value : 'general',
            '.',
            $this->translationStringType->value,
            $this->property ? ('.' . SnakeCaseSlug::fromClass($this->property)) : '',
        ];
        return $this->build($segments);
    }

    public function makeAllVariations(): TranslationStringSet
    {
        $operationTypes = $this->operationType
            ? [$this->operationType->value]
            : TranslationStringOperationType::stringCases();
        $allSegments = array_map(
            function (string $operationType) {
                return [
                    'apie.',
                    $this->boundedContextId ? ['bounded.' . $this->boundedContextId . '.', ''] : '',
                    'resource.',
                    SnakeCaseSlug::fromClass($this->resourceName)->toNative(),
                    '.',
                    $operationType,
                    '.',
                    $this->translationStringType
                        ? $this->translationStringType->value
                        : TranslationStringType::stringCasesFor($this->resourceName, TranslationStringOperationType::tryFrom($operationType)),
                ];
            },
            $operationTypes
        );
        return $this->build(...$allSegments);
    }

    public function singular(): self
    {
        $clone = clone $this;
        $clone->translationStringType = TranslationStringType::Singular;
        $clone->property = null;
        return $clone;
    }

    public function plural(): self
    {
        $clone = clone $this;
        $clone->translationStringType = TranslationStringType::Plural;
        $clone->property = null;
        return $clone;
    }

    public function withProperty(ReflectionProperty $property): self
    {
        $clone = clone $this;
        $clone->translationStringType = TranslationStringType::Properties;
        $clone->property = $property;
        return $clone;
    }

    public function withPlaceholder(ReflectionProperty $property): self
    {
        $clone = clone $this;
        $clone->translationStringType = TranslationStringType::Placeholders;
        $clone->property = $property;
        return $clone;
    }

    public function withOperationType(TranslationStringOperationType $operationType): self
    {
        $clone = clone $this;
        $clone->operationType = $operationType;
        return $clone;
    }
}