<?php

namespace Apie\Core\PluginInterfaces;

use Apie\ObjectAccessNormalizer\ObjectAccess\ObjectAccessInterface;

interface ObjectAccessProviderInterface
{
    /**
     * @return ObjectAccessInterface[]
     */
    public function getObjectAccesses(): array;
}
