<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Identifiers\CamelCaseSlug;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\TranslationString;

/**
 * Adds default translation for properties
 */
class DefaultLabelPropertyTranslator implements ApieTranslatorInterface
{
    public function getGeneralTranslation(ApieContext $context, TranslationString|TranslationStringSet $translations): ?string
    {
        if ($translations instanceof TranslationString) {
            $translations = new TranslationStringSet([$translations]);
        }
        foreach ($translations as $translation) {
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
