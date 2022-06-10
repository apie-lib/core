<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\Utils;
use Apie\Core\ValueObjects\ValueObjectInterface;
use Apie\Tests\Core\Fixtures\AbstractClassExample;
use Apie\Tests\Core\Fixtures\AbstractInterface;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class UtilsTest extends TestCase
{
    public function testGetDisplayNameForValueObject()
    {
        $this->assertEquals(
            'ClassExample',
            Utils::getDisplayNameForValueObject(new ReflectionClass(AbstractClassExample::class))
        );
        $this->assertEquals(
            'ValueObject',
            Utils::getDisplayNameForValueObject(new ReflectionClass(ValueObjectInterface::class))
        );
        $this->assertEquals(
            'Abstract',
            Utils::getDisplayNameForValueObject(new ReflectionClass(AbstractInterface::class))
        );
    }
}
