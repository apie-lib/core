<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\FakeMethod;
use Faker\Generator;
use Ramsey\Uuid\Uuid as RamseyUuid;

#[FakeMethod("createRandom")]
class UuidV2 extends Uuid
{
    public static function createRandom(Generator $generator): self
    {
        $uuid2 = RamseyUuid::uuid2(
            $generator->randomElement([
                RamseyUuid::DCE_DOMAIN_PERSON,
                RamseyUuid::DCE_DOMAIN_GROUP,
            ])
        );

        return static::fromNative($uuid2->toString());
    }
}
