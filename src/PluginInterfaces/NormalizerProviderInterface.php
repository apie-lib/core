<?php


namespace Apie\Core\PluginInterfaces;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

interface NormalizerProviderInterface
{
    /**
     * @return (NormalizerInterface|DenormalizerInterface)[]
     */
    public function getNormalizers(): array;
}
