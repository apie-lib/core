<?php
namespace Apie\Tests\Core\Repositories;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Exceptions\EntityAlreadyPersisted;
use Apie\Core\Exceptions\EntityNotFoundException;
use Apie\Core\Exceptions\UnknownExistingEntityError;
use Apie\Core\ValueObjects\DatabaseText;
use Apie\Fixtures\Entities\UserWithAutoincrementKey;
use Apie\Fixtures\Identifiers\UserAutoincrementIdentifier;
use Apie\Fixtures\TestHelpers\TestWithInMemoryDatalayer;
use Apie\Fixtures\ValueObjects\AddressWithZipcodeCheck;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class InMemoryDatalayerTest extends TestCase
{
    use TestWithInMemoryDatalayer;
    /**
     * @test
     */
    public function it_can_remember_entities()
    {
        $testItem = $this->givenAnInMemoryDataLayer(new BoundedContextId('default'));
        $this->assertEquals(
            [],
            iterator_to_array($testItem->all(new ReflectionClass(UserWithAutoincrementKey::class)))
        );
        $user = new UserWithAutoincrementKey(
            new AddressWithZipcodeCheck(
                new DatabaseText('street'),
                new DatabaseText('42'),
                new DatabaseText('1341'),
                new DatabaseText('Amsterdam')
            )
        );
        $testItem->persistNew($user);
        $this->assertEquals(
            [$user],
            iterator_to_array($testItem->all(new ReflectionClass(UserWithAutoincrementKey::class)))
        );
        $this->expectException(EntityAlreadyPersisted::class);
        $testItem->persistNew($user);
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_an_entity_can_not_be_found()
    {
        $testItem = $this->givenAnInMemoryDataLayer(new BoundedContextId('default'));
        $this->expectException(EntityNotFoundException::class);
        $testItem->find(new UserAutoincrementIdentifier(12));
    }

    /**
     * @test
     */
    public function it_throws_an_error_if_the_entity_can_not_be_found_that_requires_update()
    {
        $testItem = $this->givenAnInMemoryDataLayer(new BoundedContextId('default'));
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
