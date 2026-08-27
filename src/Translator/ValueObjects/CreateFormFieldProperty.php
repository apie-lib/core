<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\StringList;

/**
 * Translation for a form field on the create resource form.
 */
final class CreateFormFieldProperty extends AbstractTranslation
{
    protected const MIDDLE_REGEX = 'create\.[^.]+(\.[^.]+)*';

    public function getFallbackText(): string
    {
        return strstr($this->middleSection, '.');
    }

    /**
     * @param \ReflectionClass<covariant object> $class
     */
    public static function createForProperty(
        StringList $list,
        \ReflectionClass $class,
        ?BoundedContextId $boundedContextId = null
    ): static {
        return new CreateFormFieldProperty(
            new TranslationStringPrefix($boundedContextId, SnakeCaseSlug::fromClass($class)),
            'create.' . $list->join('.'),
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }
}
