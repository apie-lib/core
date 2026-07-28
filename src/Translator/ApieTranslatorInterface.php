<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\Lists\TranslationStringSet;
use Apie\Core\Translator\ValueObjects\AbstractTranslation;
use Apie\Core\Translator\ValueObjects\TranslationString;

interface ApieTranslatorInterface
{
    public function getGeneralTranslation(ApieContext $context, AbstractTranslation|TranslationString|TranslationStringSet $translation): ?string;
}
