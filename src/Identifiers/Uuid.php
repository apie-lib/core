<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\Description;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use GMP;

#[Description('Uuid written in 8-4-4-4-12 text format')]
class Uuid implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        return '/^[a-f0-9]{8}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{4}\-[a-f0-9]{12}$/';
    }

    public static function generateFromInteger(int|string|GMP $number): static
    {
        if (is_int($number) && $number <= 0xFFFFFFFFFFFF) {
            return static::fromNative(
                sprintf(
                    '00000000-0000-0000-0000-%012x',
                    $number
                )
            );
        }
        $hex = gmp_strval(gmp_init($number, 10), 16);
        $hex = str_pad($hex, 32, '0', STR_PAD_LEFT);
        return static::fromNative(
            sprintf(
                '%s-%s-%s-%s-%s',
                substr($hex, 0, 8),
                substr($hex, 8, 4),
                substr($hex, 12, 4),
                substr($hex, 16, 4),
                substr($hex, 20, 12)
            )
        );
    }
}
