<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\AbstractTranslation;

/**
 * Adds default translation for properties
 */
class DefaultLabelPropertyTranslator implements ApieTranslatorInterface
{
    public function getGeneralTranslation(ApieContext $context, AbstractTranslation|TranslationStringSet $translations): ?string
    {
        if ($translations instanceof AbstractTranslation) {
            // @phpstan-ignore-next-line argument.count
            return $translations->getFallbackText($context->getContext(ContextConstants::ACCEPT_LOCALE, false));
        }
        foreach ($translations as $translation) {
            // @phpstan-ignore-next-line argument.count
            return $translation->getFallbackText($context->getContext(ContextConstants::ACCEPT_LOCALE, false));
        }
        return null;
    }
}
