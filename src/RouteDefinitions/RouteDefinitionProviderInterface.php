<?php
namespace Apie\Core\RouteDefinitions;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\Context\ApieContext;

interface RouteDefinitionProviderInterface
{
    public function getActionsForBoundedContext(BoundedContext $boundedContext, ApieContext $apieContext): ActionHashmap;
}
