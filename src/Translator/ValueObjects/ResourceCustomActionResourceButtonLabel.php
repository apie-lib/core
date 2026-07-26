<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Label shown on button for a domain specific action')]
#[ExampleValue('apie.bounded.test.example.user.action.custom.1234.deactivate.label.authenticated')]
class ResourceCustomActionResourceButtonLabel extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.custom\.:id\.:action\.label';

    public function getFallbackText(): string
    {
        return $this->getPlaceholders()['action'] ?? 'action';
    }
}
