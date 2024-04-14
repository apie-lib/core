<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\Enums\FromFileLanguage;
use Apie\Core\Translator\ValueObjects\TranslationString;

class FromFileTranslator implements ApieTranslatorInterface
{
    public function __construct(private readonly string $translationPath)
    {
    }

    public static function createFallback(): self
    {
        return new self(__DIR__ . '/../../lang/');
    }

    public function getGeneralTranslation(ApieContext $context, TranslationString $translation): ?string
    {
        $language = FromFileLanguage::fromContext($context);
        $fullPath = $translation->toPath(rtrim($this->translationPath, '/') . '/' . $language->value) . '.php';
        if (file_exists($fullPath)) {
            return include $fullPath;
        }
        return null;
    }
}
