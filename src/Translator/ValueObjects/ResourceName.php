<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Actions\BoundedContextEntityTuple;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Translator\Enums\Pluralization;
use ICanBoogie\Inflector;

#[Description('Name of resource')]
#[ExampleValue('apie.bounded.test.resource.user.name.singular')]
class ResourceName extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'name';

    public function getFallbackText(string $locale = 'en'): string
    {
        $id = $this->prefix->getResourceIdentifier();
        $plural = $this->suffix->getPluralization();
        if ($id) {
            $inflector = Inflector::get($locale);
            return match ($plural) {
                Pluralization::Singular => $inflector->singularize(ucfirst($id->humanize())),
                Pluralization::Plural => $inflector->pluralize(ucfirst($id->humanize())),
                default => ucfirst($id->humanize()),
            };
        }
        return 'Resource';
    }

    public static function createFromTuple(BoundedContextEntityTuple $tuple, TranslationStringSuffix $suffix = new TranslationStringSuffix()): static
    {
        return new static(
            new TranslationStringPrefix(
                $tuple->boundedContext->getId(),
                SnakeCaseSlug::fromClass($tuple->resourceClass)
            ),
            'name',
            $suffix,
            new ItemHashmap()
        );
    }
}
