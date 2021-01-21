<?php

namespace Apie\Tests\Core\Exceptions;

use Apie\Core\Exceptions\LocalizationInfo;
use PHPUnit\Framework\TestCase;

class LocalizationInfoTest extends TestCase
{
    public function testItWorks()
    {
        $testItem = new LocalizationInfo('test.identifier', ['id' => 1], 12);
        $this->assertEquals('test.identifier', $testItem->getMessageString());
        $this->assertEquals(['id' => 1], $testItem->getReplacements());
        $this->assertEquals(12, $testItem->getAmount());
    }
}