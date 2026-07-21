<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Header shown on add resource form')]
#[ExampleValue('apie.bounded.test.example.user.resource.add.header.authenticated')]
class ResourceAddResourceHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'resource.add.header';

    public function getFallbackText(): string
    {
        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return 'Create ' . $id->humanize();
        }
        return 'Create';
    }
}
