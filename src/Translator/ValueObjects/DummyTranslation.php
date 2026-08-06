<?php
namespace Apie\Core\Translator\ValueObjects;

class DummyTranslation extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'mid-section\.parent';

    public function getFallbackText(): string
    {
        return 'Dummy';
    }
}
