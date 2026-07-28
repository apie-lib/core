<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Common\ActionDefinitions\RunResourceMethodDefinition;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

#[Description('Header shown on form for a domain specific action')]
#[ExampleValue('apie.bounded.test.example.user.action.custom.1234.deactivate.header.authenticated')]
class ResourceCustomActionResourceHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'action\.custom\.:id\.:action\.header';

    public function getFallbackText(): string
    {
        $resourceId = $this->getPlaceholders()['id'] ?? null;
        $action = ucfirst(SnakeCaseSlug::fromText($this->getPlaceholders()['action'] ?? 'action'));
        
        $suffix = $resourceId ? (' on ' . $resourceId) : '';
        $id = $this->prefix->getResourceIdentifier();
        if ($id) {
            return $id . ' ' . $action . $suffix;
        }
        return ucfirst($action) . $suffix;
    }

    public static function createFromDefinition(RunResourceMethodDefinition $actionDefinition, ?bool $authenticated): static
    {
        $action = SnakeCaseSlug::fromClass($actionDefinition->getMethod());
        return new static(
            new TranslationStringPrefix(
                $actionDefinition->getBoundedContextId(),
                SnakeCaseSlug::fromClass($actionDefinition->getResourceName())
            ),
            'action.custom.:id.' . $action . '.header',
            new TranslationStringSuffix(null, $authenticated),
            new ItemHashmap(['action' => $action])
        );
    }
}
