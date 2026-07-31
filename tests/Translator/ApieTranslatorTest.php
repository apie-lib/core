<?php
namespace Apie\Tests\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\ApieTranslator;
use Apie\Core\Translator\ValueObjects\AuditLogEventMessage;
use Apie\Core\Translator\ValueObjects\DummyTranslation;
use PHPUnit\Framework\TestCase;

class ApieTranslatorTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_fallback_text_if_not_found()
    {
        $testItem = ApieTranslator::create();
        $actual = $testItem->getGeneralTranslation(
            new ApieContext(),
            DummyTranslation::fromNative('apie.mid-section.parent')
        );
        $this->assertEquals('Dummy', $actual);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_translation_if_found()
    {
        $testItem = ApieTranslator::create();
        $actual = $testItem->getGeneralTranslation(
            new ApieContext(),
            AuditLogEventMessage::createResourceCreatedEvent(new ApieContext())
        );
        $this->assertEquals('Added resource', $actual);
    }
}
