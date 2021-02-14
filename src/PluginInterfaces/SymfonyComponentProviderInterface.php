<?php


namespace Apie\Core\PluginInterfaces;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

interface SymfonyComponentProviderInterface
{
    public function getClassMetadataFactory(): ClassMetadataFactoryInterface;
    public function getPropertyConverter(): NameConverterInterface;
}
