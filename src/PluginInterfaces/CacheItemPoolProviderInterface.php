<?php


namespace Apie\Core\PluginInterfaces;

use Psr\Cache\CacheItemPoolInterface;

interface CacheItemPoolProviderInterface
{
    public function getCacheItemPool(): CacheItemPoolInterface;
}
