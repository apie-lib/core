<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\ValueObjects\TranslationString;

class ApieTranslator implements ApieTranslatorInterface
{
    /**
     * @var array<int, ApieTranslatorInterface>
     */
    private array $translators;

    public function __construct(
        ApieTranslatorInterface... $translators
    ) {
        $this->translators = $translators;
    }

    public static function create(): self
    {
        return new self(
            FromFileTranslator::createFallback()
        );
    }

    public function getGeneralTranslation(ApieContext $context, TranslationString $translation): string
    {
        foreach ($this->translators as $translator) {
            $res = $translator->getGeneralTranslation($context, $translation);
            if ($res !== null) {
                return $res;
            }
        }

        return $translation;
    }
}
