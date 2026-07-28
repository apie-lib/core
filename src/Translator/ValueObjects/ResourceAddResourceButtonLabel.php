<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Common\ActionDefinitions\CreateResourceActionDefinition;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

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

    public static function createFromDefinition(CreateResourceActionDefinition $actionDefinition, ?bool $authenticated): static
    {
        return new static(
            new TranslationStringPrefix(
                $actionDefinition->getBoundedContextId(),
                SnakeCaseSlug::fromClass($actionDefinition->getResourceName())
            ),
            'action.add.label',
            new TranslationStringSuffix(null, $authenticated),
            new ItemHashmap()
        );
    }
}
