<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Header shown on form for a domain specific action')]
#[ExampleValue('apie.bounded.test.example.user.action.global.create_admin.header.authenticated')]
class ResourceCustomGlobalActionResourceHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.global\.:action\.header';

    public function getFallbackText(): string
    {
        return ucfirst($this->getPlaceholders()['action'] ?? 'action');
    }
}
