<?php
namespace Apie\Core\Dto;

use Apie\Core\Actions\ActionInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionType;

/**
 * Used internally to indicate a paginated result of some object.
 *
 * @see ActionInterface::getInputType()
 * @see ActionInterface::getOutputType()
 */
final class ListOf
{
    /**
     * @param ReflectionClass<object>|ReflectionMethod|ReflectionType $type
     */
    public function __construct(
        public readonly ReflectionClass|ReflectionMethod|ReflectionType $type
    ) {
    }
}
