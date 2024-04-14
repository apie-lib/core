<?php
namespace Apie\Core\Translator;

use Apie\Core\Context\ApieContext;
use Apie\Core\Translator\ValueObjects\TranslationString;

interface ApieTranslatorInterface
{
    public function getGeneralTranslation(ApieContext $context, TranslationString $translation): ?string;
}
