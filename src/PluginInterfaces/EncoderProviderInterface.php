<?php

namespace Apie\Core\PluginInterfaces;

use Apie\Core\Interfaces\FormatRetrieverInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

interface EncoderProviderInterface
{
    /**
     * @return (EncoderInterface|DecoderInterface)[]
     */
    public function getEncoders(): array;

    /**
     * @return FormatRetrieverInterface
     */
    public function getFormatRetriever(): FormatRetrieverInterface;
}
