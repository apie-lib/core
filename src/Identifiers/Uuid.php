<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\Description;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;

#[Description('Uuid written in 8-4-4-4-12 text format')]
class Uuid implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-f0-9]{8}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}$/';
    }
}
