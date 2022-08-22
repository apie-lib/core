<?php
namespace Apie\Core\Actions;

use Apie\Core\Context\ApieContext;

/**
 * Common interface for actions. Actions are the parts that actually do something in Apie and are used internally by high-level
 * functionality, for example all Rest API calls use actions internally.
 */
interface ActionInterface
{
    /**
     * @param array<string|int, mixed> $rawContents
     */
    public function __invoke(ApieContext $context, array $rawContents): ActionResponse;
}
