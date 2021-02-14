<?php

namespace Apie\Core\PluginInterfaces;

use Doctrine\Common\Annotations\Reader;

interface AnnotationReaderProviderInterface
{
    public function getAnnotationReader(): Reader;
}
