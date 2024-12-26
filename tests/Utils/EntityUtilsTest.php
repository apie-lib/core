<?php
namespace Apie\Tests\Core\Utils;

use Apie\Core\Utils\EntityUtils;
use Apie\Fixtures\Entities\Polymorphic\Animal;
use Apie\Fixtures\Entities\Polymorphic\Cow;
use Apie\IntegrationTests\Apie\TypeDemo\Entities\Ostrich;
use Apie\IntegrationTests\Apie\TypeDemo\Identifiers\AnimalIdentifier;
use Apie\IntegrationTests\Apie\TypeDemo\Resources\Animal as IntegrationTestAnimal;
use Apie\TextValueObjects\FirstName;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class EntityUtilsTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_return_discriminator_mappings()
    {
        $this->assertEquals(['animalType' => 'cow'], EntityUtils::getDiscriminatorValues(new Cow()));
        if (class_exists(IntegrationTestAnimal::class)) {
            $ostrich = new Ostrich(AnimalIdentifier::createRandom(), new FirstName('Albert'));
            $this->assertEquals(['type' => 'bird', 'name' => 'ostrich'], EntityUtils::getDiscriminatorValues($ostrich));
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_can_find_the_actual_class_from_discriminator_mappings()
    {
        $this->assertEquals(
            Cow::class,
            EntityUtils::findClass(
                ['animalType' => 'cow'],
                new ReflectionClass(Animal::class)
            )->name
        );
        if (class_exists(IntegrationTestAnimal::class)) {
            $this->assertEquals(
                Ostrich::class,
                EntityUtils::findClass(
                    ['type' => 'bird', 'name' => 'ostrich'],
                    new ReflectionClass(IntegrationTestAnimal::class)
                )->name
            );
        }
    }
}
