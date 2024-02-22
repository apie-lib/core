<?php
namespace Apie\Core;

use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\Utils;
use Apie\SchemaGenerator\Other\JsonSchemaFormatValidator;
use League\OpenAPIValidation\Schema\TypeFormats\FormatsContainer;
use ReflectionClass;

final class ApieLib
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public const VERSION = '1.0-dev';

    public const APIE_FORM_ELEMENTS = '0.1.1';

    public const APIE_STACKTRACE = '0.1.6';


    /**
     * Workaround for many integration tests in validating string value objects in OneOf or AllOf schema's.
     *
     * @param class-string<ValueObjectInterface> $class
     */
    public static function registerValueObject(string $class): void
    {
        if (class_exists(FormatsContainer::class) && class_exists(JsonSchemaFormatValidator::class)) {
            $format = strtolower(Utils::getDisplayNameForValueObject(new ReflectionClass($class)));
            if (!FormatsContainer::getFormat('string', $format)) {
                FormatsContainer::registerFormat('string', $format, new JsonSchemaFormatValidator($class));
            }
            if (!FormatsContainer::getFormat('number', $format)) {
                FormatsContainer::registerFormat('number', $format, new JsonSchemaFormatValidator($class));
            }
        }
    }
}
