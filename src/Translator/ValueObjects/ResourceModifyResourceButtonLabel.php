<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Text shown on edit resource button')]
#[ExampleValue('apie.bounded.test.example.user.action.edit.:id.label.authenticated')]
class ResourceModifyResourceButtonLabel extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.edit\.:id\.label';

    public function getFallbackText(): string
    {
        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return 'Edit ' . $id->humanize();
        }
        return 'Edit';
    }
}
