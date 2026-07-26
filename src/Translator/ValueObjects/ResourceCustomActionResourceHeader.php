<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Header shown on form for a domain specific action')]
#[ExampleValue('apie.bounded.test.example.user.action.custom.1234.deactivate.header.authenticated')]
class ResourceCustomActionResourceHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.custom\.:id\.:action\.header';

    public function getFallbackText(): string
    {
        $resourceId = $this->getPlaceholders()['id'] ?? null;
        $action = $this->getPlaceholders()['action'] ?? 'action';
        
        $suffix = $resourceId ? (' on ' . $resourceId) : '';
        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return $id . ' ' . $action . $suffix;
        }
        return ucfirst($action) . $suffix;
    }
}
