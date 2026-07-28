<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Identifiers\CamelCaseSlug;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\AbstractTranslation;
use Apie\Core\Translator\ValueObjects\TranslationString;

/**
 * Adds default translation for properties
 */
class DefaultLabelPropertyTranslator implements ApieTranslatorInterface
{
    public function getGeneralTranslation(ApieContext $context, TranslationString|AbstractTranslation|TranslationStringSet $translations): ?string
    {
        if ($translations instanceof TranslationString) {
            $translations = new TranslationStringSet([$translations]);
        }
        if ($translations instanceof AbstractTranslation) {
            // @phpstan-ignore-next-line argument.count
            return $translations->getFallbackText($context->getContext(ContextConstants::ACCEPT_LOCALE, false));
        }
        foreach ($translations as $translation) {
            if ($translation instanceof AbstractTranslation) {
                // @phpstan-ignore-next-line argument.count
                return $translation->getFallbackText($context->getContext(ContextConstants::ACCEPT_LOCALE, false));
            }
            if ($this->isPropertyTranslation($translation)) {
                return ucfirst((new CamelCaseSlug($translation->getLastTranslationSegment()))->humanize());
            }
        }
        return null;
    }

    private function isPropertyTranslation(TranslationString $translation): bool
    {
        return (bool) preg_match('/^apie\.(bounded|resource)\..*\.properties.*(\.([a-z0-9]|__)[a-z0-9_]*)$/', $translation->toNative());
    }
}
