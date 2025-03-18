<?php
namespace Apie\Core\Attributes;

use Apie\Core\RegexUtils;
use Apie\Core\ValueObjects\Utils;
use Attribute;
use ReflectionClass;
use ReflectionProperty;

#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
final class CmsValidationCheck
{
    private mixed $exactMatch;

    public function __construct(
        public ?string $message = null,
        public bool $inverseCheck = false,
        public readonly ?string $pattern = null,
        public readonly ?string $patternMethod = null,
        public readonly ?string $minLengthMethod = null,
        public readonly ?string $maxLengthMethod = null,
    ) {
    }

    public static function createFromStaticValue(string $message, mixed $value): self
    {
        $res = new self(message: $message);
        $res->exactMatch = $value;
        return $res;
    }

    /**
     * @param ReflectionClass<object> $class
     * @return array<string, mixed>
     */
    public function toArray(ReflectionClass $class): array
    {
        $res = [
            'message' => $this->message,
            'inverseCheck' => $this->inverseCheck,
            'pattern' => $this->pattern,
        ];
        if ((new ReflectionProperty($this, 'exactMatch'))->isInitialized($this)) {
            $res['exactMatch'] = $this->exactMatch;
        }
        foreach (get_object_vars($this) as $propertyName => $propertyValue) {
            if (str_ends_with($propertyName, 'Method') && is_string($propertyValue)) {
                $method = $class->getMethod($propertyValue);
                $res[preg_replace('/Method$/', '', $propertyName)] = $this->sanitize(
                    $propertyName,
                    $method->invoke(null)
                );
            }
        }
        return $res;
    }

    private function sanitize(string $propertyName, mixed $value): mixed
    {
        if ($propertyName === 'patternMethod') {
            return RegexUtils::removeDelimiters(Utils::toString($value));
        }
        return $value;
    }
}
