<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\StringList;

final class FormFieldProperty extends AbstractTranslation
{
    protected const MIDDLE_REGEX = '[^.]+(\.[^.]+)*';

    public function getFallbackText(): string
    {
        return strstr($this->middleSection, '.');
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    public static function createForProperty(
        StringList $list,
        \ReflectionClass $class,
        ?BoundedContextId $boundedContextId = null
    ): static {
        return new FormFieldProperty(
            new TranslationStringPrefix($boundedContextId, SnakeCaseSlug::fromClass($class)),
            $list->join('.'),
            new TranslationStringSuffix(),
            new ItemHashmap()
        );
    }
}
