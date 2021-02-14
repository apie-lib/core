<?php

namespace Apie\Core\PluginInterfaces;

use Apie\Core\Interfaces\ApiResourceFactoryInterface;

interface ApiResourceFactoryProviderInterface
{
    public function getApiResourceFactory(): ApiResourceFactoryInterface;
}
