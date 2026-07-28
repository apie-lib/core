<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Common\ActionDefinitions\CreateResourceActionDefinition;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

#[Description('Header shown on add resource form')]
#[ExampleValue('apie.bounded.test.example.user.action.add.header.authenticated')]
class ResourceAddResourceHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.add\.header';

    public function getFallbackText(): string
    {
        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return 'Create ' . $id->humanize();
        }
        return 'Create';
    }

    public static function createFromDefinition(CreateResourceActionDefinition $actionDefinition, ?bool $authenticated): static
    {
        return new static(
            new TranslationStringPrefix(
                $actionDefinition->getBoundedContextId(),
                SnakeCaseSlug::fromClass($actionDefinition->getResourceName())
            ),
            'action.add.header',
            new TranslationStringSuffix(null, $authenticated),
            new ItemHashmap()
        );
    }
}
