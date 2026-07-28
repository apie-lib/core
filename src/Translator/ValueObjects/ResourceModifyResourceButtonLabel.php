<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Common\ActionDefinitions\ModifyResourceActionDefinition;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

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

    public static function createFromDefinition(ModifyResourceActionDefinition $actionDefinition, ?bool $authenticated): static
    {
        return new static(
            new TranslationStringPrefix(
                $actionDefinition->getBoundedContextId(),
                SnakeCaseSlug::fromClass($actionDefinition->getResourceName())
            ),
            'action.edit.:id.label',
            new TranslationStringSuffix(null, $authenticated),
            new ItemHashmap()
        );
    }
}
