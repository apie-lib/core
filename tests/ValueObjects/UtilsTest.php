<?php
namespace Apie\Tests\Core\ValueObjects;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use Apie\Fixtures\Other\AbstractClassExample;
use Apie\Fixtures\Other\AbstractInterface;
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
