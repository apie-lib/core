<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;

#[Description('Represents a url route definition with placeholders, for example "/test/{id}"')]
class UrlRouteDefinition implements StringValueObjectInterface
{
    use IsStringValueObject;

    protected function convert(string $input): string
    {
        if (substr($input, 0, 1) !== '/') {
            return '/' . $input;
        }
        return $input;
    }

    public function withBaseUrl(string $baseUrl): self
    {
        return new self(rtrim($baseUrl, '/') . $this->internal);
    }

    /**
     * @return array<int, string>
     */
    public function getPlaceholders(): array
    {
        if (preg_match_all('/\{\s*(?<placeholder>[a-zA-Z0-9_]+)\s*\}/', $this->internal, $matches)) {
            return $matches['placeholder'];
        }
        return [];
    }
}
