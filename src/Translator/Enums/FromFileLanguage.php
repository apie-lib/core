<?php
namespace Apie\Core\Translator\Enums;

use Apie\Core\Context\ApieContext;

enum FromFileLanguage: string
{
    case EN = 'en';
    case NL = 'nl';

    public static function fromContext(ApieContext $apieContext): self
    {
        $locale = $apieContext->getContext('locale', false) ?? 'en';
        if (strpos($locale, '_')) {
            $locale = substr($locale, strpos($locale, '_'));
        }
        return FromFileLanguage::tryFrom($locale) ?? self::EN;
    }
}
