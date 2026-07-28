<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Common\ActionDefinitions\ModifyResourceActionDefinition;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

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

    public static function createFromDefinition(ModifyResourceActionDefinition $actionDefinition, ?bool $authenticated): static
    {
        return new static(
            new TranslationStringPrefix(
                $actionDefinition->getBoundedContextId(),
                SnakeCaseSlug::fromClass($actionDefinition->getResourceName())
            ),
            'action.edit.:id.header',
            new TranslationStringSuffix(null, $authenticated),
            new ItemHashmap()
        );
    }
}
