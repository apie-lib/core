<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\FakeMethod;
use Faker\Generator;
use Ramsey\Uuid\Uuid as RamseyUuid;

#[FakeMethod("createRandom")]
class UuidV5 extends Uuid
{
    public static function createRandom(Generator $generator): self
    {
        return new self(RamseyUuid::uuid5(
            RamseyUuid::NAMESPACE_URL,
            $generator->url()
        )->toString());
    }
}
