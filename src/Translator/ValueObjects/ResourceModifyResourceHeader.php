<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Header shown on edit resource button')]
#[ExampleValue('apie.bounded.test.example.user.action.edit.:id.header.authenticated')]
class ResourceModifyResourceHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.edit\.:id\.header';

    public function getFallbackText(): string
    {
        $resourceId = $this->getPlaceholders()['id'] ?? null;
        $suffix = $resourceId ? (' ' . $resourceId) : '';

        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return 'Edit ' . $id->humanize() . $suffix;
        }
        return 'Edit' . $suffix;
    }
}
