<?php
namespace Apie\Tests\Core\Repositories;

use Apie\CompositeValueObjects\CompositeValueObject;
use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\InMemory\InMemoryDatalayer;
use Apie\Core\Exceptions\EntityAlreadyPersisted;
use Apie\Core\Exceptions\EntityNotFoundException;
use Apie\Core\Exceptions\UnknownExistingEntityError;
use Apie\Fixtures\Entities\UserWithAutoincrementKey;
use Apie\Fixtures\Identifiers\UserAutoincrementIdentifier;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use Apie\TextValueObjects\DatabaseText;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class InMemoryDatalayerTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_remember_entitites()
    {
        if (!trait_exists(CompositeValueObject::class)) {
            $this->markTestSkipped('requires apie/composite-value-objects');
        }
        $testItem = new InMemoryDatalayer(new BoundedContextId('default'));
        $this->assertEquals([], $testItem->all(new ReflectionClass(UserWithAutoincrementKey::class))->take(0, 100));
        $user = new UserWithAutoincrementKey(
            new AddressWithZipcodeCheck(
                new DatabaseText('street'),
                new DatabaseText('42'),
                new DatabaseText('1341'),
                new DatabaseText('Amsterdam')
            )
        );
        $testItem->persistNew($user);
        $this->assertEquals([$user], $testItem->all(new ReflectionClass(UserWithAutoincrementKey::class))->take(0, 100));
        $this->expectException(EntityAlreadyPersisted::class);
        $testItem->persistNew($user);
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_an_entity_can_not_be_found()
    {
        if (!trait_exists(CompositeValueObject::class)) {
            $this->markTestSkipped('requires apie/composite-value-objects');
        }
        $testItem = new InMemoryDatalayer(new BoundedContextId('default'));
        $this->expectException(EntityNotFoundException::class);
        $testItem->find(new UserAutoincrementIdentifier(12));
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_the_entity_can_not_be_found_that_requires_update()
    {
        if (!trait_exists(CompositeValueObject::class)) {
            $this->markTestSkipped('requires apie/composite-value-objects');
        }
        $testItem = new InMemoryDatalayer(new BoundedContextId('default'));
        $user = new UserWithAutoincrementKey(
            new AddressWithZipcodeCheck(
                new DatabaseText('street'),
                new DatabaseText('42'),
                new DatabaseText('1341'),
                new DatabaseText('Amsterdam')
            )
        );
        $this->expectException(UnknownExistingEntityError::class);
        $testItem->persistExisting($user);
    }
}
