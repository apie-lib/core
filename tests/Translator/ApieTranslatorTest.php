<?php
namespace Apie\Tests\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\ApieTranslator;
use Apie\Core\Translator\ValueObjects\TranslationString;
use PHPUnit\Framework\TestCase;

class ApieTranslatorTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_translation_string_if_not_found()
    {
        $testItem = ApieTranslator::create();
        $actual = $testItem->getGeneralTranslation(
            new ApieContext(),
            new TranslationString('does_not_exist')
        );
        $this->assertEquals('does_not_exist', $actual);
    }

    /**
     * @test
     */
    public function it_returns_translation_if_found()
    {
        $testItem = ApieTranslator::create();
        $actual = $testItem->getGeneralTranslation(
            new ApieContext(),
            new TranslationString('apie.cms.bounded_context')
        );
        $this->assertEquals('Bounded context', $actual);
    }
}
