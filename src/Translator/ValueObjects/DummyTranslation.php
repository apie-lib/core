<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Translator\ValueObjects\AbstractTranslation;

class DummyTranslation extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'mid-section\.parent';

    public function getFallbackText(): string
    {
        return 'Dummy';
    }
}
