<?php

namespace Apie\Core\PluginInterfaces;

use Apie\OpenapiSchema\Contract\InfoContract;

interface OpenApiInfoProviderInterface
{
    public function createInfo(): InfoContract;
}
