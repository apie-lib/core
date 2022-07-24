<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\ValueObjects\Interfaces\StringValueObjectInterface;

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
}
