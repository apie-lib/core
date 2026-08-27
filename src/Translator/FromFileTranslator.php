<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;
use Apie\Core\Identifiers\KebabCaseSlug;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\AbstractTranslation;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Utils;

class FromFileTranslator implements ApieTranslatorInterface
{
    public function __construct(private readonly string $translationPath)
    {
    }

    public static function createFallback(): self
    {
        return new self(__DIR__ . '/../../lang/');
    }

    public function getGeneralTranslation(ApieContext $context, AbstractTranslation|TranslationStringSet $translations): ?string
    {
        if ($translations instanceof AbstractTranslation) {
            $translations = [$translations, ...$translations->getSimplifications()];
        }
        $language = Utils::toString($context->getContext(ContextConstants::LOCALE, false) ?? 'en');
        try {
            SnakeCaseSlug::validate($language);
        } catch (InvalidStringForValueObjectException) {
            try {
                KebabCaseSlug::validate($language);
            } catch (InvalidStringForValueObjectException) {
                $language = 'en';
            }
        }

        $languages = [$language];
        if (str_contains($language, '_')) {
            $languages[] = strstr($language, '_', true);
        }
        
        foreach ($translations as $translation) {
            foreach ($languages as $language) {
                $fullPath = $translation->toPath(rtrim($this->translationPath, '/') . '/' . $language) . '.php';
                if (file_exists($fullPath)) {
                    return include $fullPath;
                }
            }
        }
        return null;
    }
}
