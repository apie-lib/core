<?php


namespace Apie\Core\PluginInterfaces;

use Apie\OpenapiSchema\Spec\Document;

interface OpenApiEventProviderInterface
{
    public function onOpenApiDocGenerated(Document $document): Document;
}
