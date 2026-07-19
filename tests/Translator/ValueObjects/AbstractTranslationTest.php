<?php
namespace Apie\Tests\Core\Translator\ValueObjects;

use Apie\Core\Translator\ValueObjects\AbstractTranslation;
use Apie\Fixtures\TestHelpers\TestWithFaker;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AbstractTranslationTest extends TestCase
{
    use TestWithFaker;
    #[Test]
    public function it_works_with_apie_faker()
    {
        $this->runFakerTest(AbstractTranslation::class);
    }
}
