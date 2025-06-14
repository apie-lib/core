<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;
use Symfony\Component\Uid\Ulid as SymfonyUlid;

#[FakeMethod('createRandom')]
#[Description('Ulid in base58 format')]
class Ulid implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function getRegularExpression(): string
    {
        // TODO stricter base58
        return '/^[a-zA-Z0-9]{22}$/i';
    }

    public static function createRandom(): static
    {
        $ulid = new SymfonyUlid();
        return static::fromNative($ulid->toBase58());
    }
}
