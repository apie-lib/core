<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\Lists\TranslationStringSet;
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
            new DefaultLabelPropertyTranslator(),
            FromFileTranslator::createFallback()
        );
    }

    public function getGeneralTranslation(ApieContext $context, TranslationString|TranslationStringSet $translation): string
    {
        foreach ($this->translators as $translator) {
            $res = $translator->getGeneralTranslation($context, $translation);
            if ($res !== null) {
                return $res;
            }
        }

        if ($translation instanceof TranslationStringSet) {
            foreach ($translation as $someTranslation) {
                return (string) $someTranslation;
            }
            return '(unknown)';
        }

        return (string) $translation;
    }
}
