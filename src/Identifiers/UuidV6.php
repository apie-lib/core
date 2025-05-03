<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\FakeMethod;
use Ramsey\Uuid\Uuid as RamseyUuid;

#[FakeMethod("createRandom")]
class UuidV6 extends Uuid
{
    public static function createRandom(): self
    {
        return static::fromNative(RamseyUuid::uuid6()->toString());
    }
}
