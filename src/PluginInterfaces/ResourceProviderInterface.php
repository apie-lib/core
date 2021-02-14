<?php


namespace Apie\Core\PluginInterfaces;

interface ResourceProviderInterface
{
    /**
     * Returns a list of Api resources.
     *
     * @return string[]
     */
    public function getResources(): array;
}
