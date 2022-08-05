<?php
namespace Apie\Core\RouteDefinitions;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;

class RouteDefinitionsProviderList implements RouteDefinitionProviderInterface
{
    public function __construct(private ActionHashmap $actionHashmap)
    {
    }

    public function getActionsForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): ActionHashmap
    {
        return $this->actionHashmap;
    }
}
