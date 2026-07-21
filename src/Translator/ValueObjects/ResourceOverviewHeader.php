<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Header shown on resource overview')]
#[ExampleValue('apie.bounded.test.example.user.resource.overview.authenticated')]
class ResourceOverviewHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'resource.overview';

    public function getFallbackText(): string
    {
        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return ucfirst($id->humanize());
        }
        return 'Overview';
    }
}
