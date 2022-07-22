<?php
namespace Apie\Core\Actions;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextBuilders\ContextBuilderInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionType;

interface ActionInterface extends ContextBuilderInterface
{
    /**
     * If action requires a content type (e.g. console inputs or a request POST body) this should return the type of the content.
     */
    public function getInputType(): ReflectionMethod|ReflectionType|ReflectionClass|null;
    /**
     * Return value of the action that will be serialized.
     */
    public function getOutputType(): ReflectionType|ReflectionClass|null;

    /**
     * Modify apie context
     */
    public function process(ApieContext $context): ApieContext;

    /**
     * Gets return value.
     */
    public function getValue(ApieContext $context): mixed;
}
