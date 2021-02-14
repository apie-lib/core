<?php


namespace Apie\Core\PluginInterfaces;

use Apie\Core\Interfaces\ResourceSerializerInterface;

interface SerializerProviderInterface
{
    public function getResourceSerializer(): ResourceSerializerInterface;
}
