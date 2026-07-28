<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Common\ActionDefinitions\RunResourceMethodDefinition;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

#[Description('Label shown on button for a domain specific action')]
#[ExampleValue('apie.bounded.test.example.user.action.global.create_admin.label.authenticated')]
class ResourceCustomGlobalActionResourceButtonLabel extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.global\.:action\.label';

    public function getFallbackText(): string
    {
        return ucfirst(SnakeCaseSlug::fromText($this->getPlaceholders()['action'] ?? 'action'));
    }

    public static function createFromDefinition(RunResourceMethodDefinition $actionDefinition, ?bool $authenticated): static
    {
        $action = SnakeCaseSlug::fromClass($actionDefinition->getMethod());
        return new static(
            new TranslationStringPrefix(
                $actionDefinition->getBoundedContextId(),
                SnakeCaseSlug::fromClass($actionDefinition->getResourceName())
            ),
            'action.global.' . $action . '.label',
            new TranslationStringSuffix(null, $authenticated),
            new ItemHashmap(['action' => $action])
        );
    }
}
