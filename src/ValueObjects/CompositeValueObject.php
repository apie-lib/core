<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Attributes\Internal;
use Apie\Core\ValueObjects\Fields\FieldInterface;
use Apie\Core\ValueObjects\Fields\FromProperty;
use Apie\Serializer\Exceptions\ValidationException;
use Exception;
use ReflectionClass;

/**
 * Use this trait to make a value object consisting of multiple properties.
 */
trait CompositeValueObject
{
    /**
     * @var array<string, FieldInterface>
     */
    private static array $fields;

    /**
     * @return array<string, FieldInterface>
     */
    public static function getFields(): array
    {
        if (!isset(self::$fields)) {
            $fields = [];
            $refl = new ReflectionClass(__CLASS__);
            foreach ($refl->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }
                if (!empty($property->getAttributes(Internal::class))) {
                    continue;
                }
                $fields[$property->getName()] = new FromProperty($property);
            }
            self::$fields = $fields;
        }

        return self::$fields;
    }

    public static function fromNative(mixed $input): self
    {
        $input = Utils::toArray($input);
        $refl = new ReflectionClass(__CLASS__);
        $instance = $refl->newInstanceWithoutConstructor();
        $errors = [];
        foreach (self::getFields() as $fieldName => $field) {
            try {
                if (array_key_exists($fieldName, $input)) {
                    $field->fromNative($instance, $input[$fieldName]);
                } else {
                    $field->fillMissingField($instance);
                }
            } catch (Exception $error) {
                $errors[$fieldName] = $error;
            }
        }
        if (!empty($errors)) {
            throw ValidationException::createFromArray($errors);
        }
        if (is_callable([$instance, 'validateState'])) {
            $instance->validateState();
        }
        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    public function toNative(): array
    {
        $result = [];
        foreach (self::getFields() as $fieldName => $field) {
            if ($field->isInitialized($this)) {
                $result[$fieldName] = $field->toNative($this);
            }
        }
        return $result;
    }
}
