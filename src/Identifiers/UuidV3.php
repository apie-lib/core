<?php
namespace Apie\Core\Identifiers;

use Apie\Core\Attributes\FakeMethod;
use Faker\Generator;
use Ramsey\Uuid\Uuid as RamseyUuid;

#[FakeMethod("createRandom")]
class UuidV3 extends Uuid
{
    public static function createRandom(Generator $generator): self
    {
        return new self(RamseyUuid::uuid3(
            RamseyUuid::NAMESPACE_URL,
            $generator->url()
        )->toString());
    }
}
