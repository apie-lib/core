<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Common\ActionDefinitions\RunResourceMethodDefinition;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

#[Description('Label shown on button for a domain specific action')]
#[ExampleValue('apie.bounded.test.example.user.action.custom.1234.deactivate.label.authenticated')]
class ResourceCustomActionResourceButtonLabel extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.custom\.:id\.:action\.label';

    public function getFallbackText(): string
    {
        return ucfirst(SnakeCaseSlug::fromText($this->getPlaceholders()['action'] ?? 'action')->humanize());
    }

    public static function createFromDefinition(RunResourceMethodDefinition $actionDefinition, ?bool $authenticated): static
    {
        $action = SnakeCaseSlug::fromClass($actionDefinition->getMethod());
        return new static(
            new TranslationStringPrefix(
                $actionDefinition->getBoundedContextId(),
                SnakeCaseSlug::fromClass($actionDefinition->getResourceName())
            ),
            'action.custom.:id.' . $action . '.label',
            new TranslationStringSuffix(null, $authenticated),
            new ItemHashmap(['action' => $action])
        );
    }
}
