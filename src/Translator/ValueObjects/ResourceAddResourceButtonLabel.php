<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Text shown on add resource button')]
#[ExampleValue('apie.bounded.test.resource.user.action.add.label.authenticated')]
class ResourceAddResourceButtonLabel extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.add\.label';

    public function getFallbackText(): string
    {
        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return 'Add ' . $id->humanize();
        }
        return 'Add';
    }
}
