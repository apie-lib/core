<?php
namespace Apie\Core\SearchFilters;

use Apie\Core\Exceptions\InvalidReturnTypeOfApiResourceException;
use Apie\OpenapiSchema\Contract\SchemaContract;
use Apie\OpenapiSchema\Factories\SchemaFactory;
use Apie\ValueObjects\StringEnumTrait;

/**
 * Enum that represents a php primitive typehint.
 */
class PhpPrimitive implements ValueObjectInterface
{
    use StringEnumTrait;

    const STRING = 'STRING';

    const BOOL = 'BOOL';

    const INT = 'INT';

    const FLOAT = 'FLOAT';

    /**
     * Returns Schema used in OpenApi Spec.
     */
    public function getSchemaForFilter(): SchemaContract
    {
        switch ($this->toNative()) {
            case self::BOOL:
                return SchemaFactory::createBooleanSchema();
            case self::INT:
                return SchemaFactory::createNumberSchema('int32');
            case self::FLOAT:
                return SchemaFactory::createFloatSchema();
        }

        return SchemaFactory::createStringSchema();
    }

    /**
     * Converts value to the primitive.
     *
     * @param string $value
     * @return int|float|string|bool
     */
    public function convert(string $value)
    {
        switch ($this->toNative()) {
            case self::BOOL:
                $filter = FILTER_VALIDATE_BOOLEAN;
                break;
            case self::INT:
                $filter = FILTER_VALIDATE_INT;
                break;
            case self::FLOAT:
                $filter = FILTER_VALIDATE_FLOAT;
                break;
            default:
                return $value;
        }
        $res = filter_var($value, $filter, FILTER_NULL_ON_FAILURE);
        if (is_null($res)) {
            throw new InvalidReturnTypeOfApiResourceException(null, $value, strtolower($this->toNative()));
        }
        return $res;
    }
}
