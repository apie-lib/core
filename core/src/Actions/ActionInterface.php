<?php
namespace Apie\Core\Actions;

use Apie\Core\Context\ApieContext;
use Apie\Core\Dto\ListOf;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\StringList;
use ReflectionClass;
use ReflectionMethod;
use ReflectionType;

/**
 * Common interface for actions. Actions are the parts that actually do something in Apie and are used internally by high-level
 * functionality, for example all Rest API calls use actions internally.
 */
interface ActionInterface
{
    public function __construct(ApieFacadeInterface $apieFacade);
    /**
     * @param array<string|int, mixed> $rawContents
     */
    public function __invoke(ApieContext $context, array $rawContents): ActionResponse;

    /**
     * Gets input type of action, for example it should create the object on POST or do a method call.
     *
     * @template T of EntityInterface
     * @param ReflectionClass<T> $class
     * @return ReflectionClass<T>|ReflectionMethod|ReflectionType
     */
    public static function getInputType(ReflectionClass $class): ReflectionClass|ReflectionMethod|ReflectionType;

    /**
     * Returns output type of response, for example the resource being updated or the result of the method call.
     *
     * @template T of EntityInterface
     * @param ReflectionClass<T> $class
     * @return ReflectionClass<T>|ReflectionMethod|ReflectionType|ListOf
     */
    public static function getOutputType(ReflectionClass $class): ReflectionClass|ReflectionMethod|ReflectionType|ListOf;

    /**
     * Returns possible response statuses. For example a DELETE can only be an error or an empty response.
     */
    public static function getPossibleActionResponseStatuses(): ActionResponseStatusList;

    /**
     * Returns description of action.
     *
     * @param ReflectionClass<EntityInterface> $class
     */
    public static function getDescription(ReflectionClass $class): string;

    /**
     * Returns tags of an action so a tool can combine these.
     *
     * @param ReflectionClass<EntityInterface> $class
     */
    public static function getTags(ReflectionClass $class): StringList;

    /**
     * @param ReflectionClass<EntityInterface> $class
     * @return array<string, mixed>
     */
    public static function getRouteAttributes(ReflectionClass $class): array;
}
