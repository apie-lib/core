<?php
namespace Apie\Core\Attributes;

use Apie\Common\Translator\ReadAttributeTranslationProvider;
use Attribute;

/**
 * Adding a ProvideTranslationMethod attribute allows you to specify a static method to be used
 * to create extra translations specifically for this domain object that could not be retrieved automatically.
 *
 * @see ApieMakeTranslationFileCommand
 * @see ReadAttributeTranslationProvider
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS)]
final class ProvideTranslationMethod
{
    public string $methodName;

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }
}
