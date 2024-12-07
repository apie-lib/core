<?php
namespace Apie\Core\Translator\Enums;

use Apie\Core\Context\ApieContext;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\MetadataFactory;
use ReflectionClass;

enum TranslationStringType: string
{
    case Singular = 'singular';
    case Plural = 'plural';
    case Properties = 'properties';
    case Placeholders = 'placeholders';

    public function requiresProperty(): bool
    {
        return in_array($this, [self::Properties, self::Placeholders]);
    }

    /**
     * @param ReflectionClass<object> $className
     * @return array<int, string>
     */
    public static function stringCasesFor(
        ReflectionClass $className,
        ?TranslationStringOperationType $type
    ): array
    {
        $callable = function (FieldInterface $field) {
            return $field->isField();
        };
        $propertyNames = array_keys(
            array_merge(
                ($type === null || $type->canRead()) ? array_filter(MetadataFactory::getResultMetadata($className, new ApieContext())->getHashmap()->toArray(), $callable) : [],
                ($type === null || $type->canCreate()) ? array_filter(MetadataFactory::getCreationMetadata($className, new ApieContext())->getHashmap()->toArray(), $callable) : [],
                ($type === null || $type->canUpdate()) ? array_filter(MetadataFactory::getModificationMetadata($className, new ApieContext())->getHashmap()->toArray(), $callable) : [],
            )
        );

        $cases = [];
        foreach (self::cases() as $case) {
            if ($case->requiresProperty()) {
                foreach ($propertyNames as $propertyName) {
                    $cases[] = $case->value . '.' . $propertyName;
                }
            } else {
                $cases[] = $case->value;
            }
        }
        return $cases;
    }
}