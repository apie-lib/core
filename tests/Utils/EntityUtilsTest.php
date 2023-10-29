<?php
namespace Apie\Tests\Core\Utils;

use Apie\Core\Utils\EntityUtils;
use Apie\Fixtures\Entities\Polymorphic\Cow;
use PHPUnit\Framework\TestCase;

class EntityUtilsTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_return_discriminator_mappings()
    {
        $this->assertEquals(['animalType' => 'cow'], EntityUtils::getDiscriminatorValues(new Cow()));
    }
}
