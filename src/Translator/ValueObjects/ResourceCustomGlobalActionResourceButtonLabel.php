<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Label shown on button for a domain specific action')]
#[ExampleValue('apie.bounded.test.example.user.action.global.create_admin.label.authenticated')]
class ResourceCustomGlobalActionResourceButtonLabel extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.global\.:action\.label';

    public function getFallbackText(): string
    {
        return ucfirst($this->getPlaceholders()['action'] ?? 'action');
    }
}
