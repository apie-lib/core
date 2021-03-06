<?php

namespace Apie\Core\Internal;

use Apie\Core\Apie;
use Apie\Core\ApiResourceFacade;
use Apie\Core\ApiResourceMetadataFactory;
use Apie\Core\ClassResourceConverter;
use Apie\Core\IdentifierExtractor;
use Apie\Core\PluginInterfaces\ResourceLifeCycleInterface;
use Apie\Core\Resources\ApiResources;
use Apie\OpenapiSchema\Spec\Document;

/**
 * Used by Apie to create the general Apie classes which you are not supposed to override in a plugin.
 *
 * @internal
 */
final class ApieCore
{
    /**
     * @var Apie
     */
    private $apie;

    /**
     * @var PluginContainer
     */
    private $pluginContainer;

    /**
     * @var ApiResourceRetriever|null
     */
    private $retriever;

    /**
     * @var ApiResourcePersister|null
     */
    private $persister;

    /**
     * @var ApiResourceMetadataFactory|null
     */
    private $metadataFactory;

    /**
     * @var IdentifierExtractor|null
     */
    private $identifierExtractor;

    /**
     * @var OpenApiSchemaGenerator|null
     */
    private $schemaGenerator;

    /**
     * @param Apie $apie
     * @param PluginContainer $pluginContainer
     */
    public function __construct(Apie $apie, PluginContainer $pluginContainer)
    {
        $this->apie = $apie;
        $this->pluginContainer = $pluginContainer;
    }

    /**
     * Returns the service that generated the complete OpenApi specification,
     *
     * @return OpenApiSpecGenerator
     */
    public function getOpenApiSpecGenerator(): OpenApiSpecGenerator
    {
        return new OpenApiSpecGenerator(
            new ApiResources($this->apie->getResources()),
            $this->getClassResourceConverter(),
            $this->apie->createInfo(),
            $this->getSchemaGenerator(),
            $this->getApiResourceMetadataFactory(),
            $this->getIdentifierExtractor(),
            $this->apie->getBaseUrl(),
            $this->getSubActionContainer(),
            $this->apie->getPropertyConverter(),
            function (Document $doc) {
                $this->apie->onOpenApiDocGenerated($doc);
            }
        );
    }

    public function getSubActionContainer(): SubActionContainer
    {
        $factory = new SubActionFactory($this->apie->getPropertyConverter());
        $types = [];
        foreach ($this->pluginContainer->getPluginsWithInterface(SubActionsProviderInterface::class) as $plugin) {
            /** @var SubActionsProviderInterface $plugin */
            foreach ($plugin->getSubActions() as $actionName => $actions) {
                foreach ($actions as $action) {
                    $types[$actionName][] = $action;
                }
            }
        }
        return new SubActionContainer(
            $types,
            $factory
        );
    }

    /**
     * Returns the service that generates the JSON schema of a class.
     *
     * @return OpenApiSchemaGenerator
     */
    public function getSchemaGenerator(): OpenApiSchemaGenerator
    {
        if (!$this->schemaGenerator) {
            $this->schemaGenerator = new OpenApiSchemaGenerator(
                $this->apie->getDynamicSchemaLogic(),
                $this->apie->getObjectAccess(),
                $this->apie->getClassMetadataFactory(),
                $this->apie->getPropertyConverter()
            );
            foreach ($this->apie->getDefinedStaticData() as $class => $schema) {
                $this->schemaGenerator->defineSchemaForResource($class, $schema);
            }
        }
        return $this->schemaGenerator;
    }

    private function getResponseFactory(): ApiResourceFacadeResponseFactory
    {
        return new ApiResourceFacadeResponseFactory(
            $this->apie->getResourceSerializer(),
            $this->pluginContainer->getPluginsWithInterface(ResourceLifeCycleInterface::class)
        );
    }

    /**
     * Returns the Apie resource facade to handle REST API responses.
     *
     * @return ApiResourceFacade
     */
    public function getApiResourceFacade(): ApiResourceFacade
    {
        return new ApiResourceFacade(
            $this->getResourceRetriever(),
            $this->getResourcePersister(),
            $this->getClassResourceConverter(),
            $this->apie->getResourceSerializer(),
            $this->apie->getFormatRetriever(),
            $this->getSubActionContainer(),
            $this->apie->getPropertyConverter(),
            $this->getResponseFactory(),
            $this->pluginContainer->getPluginsWithInterface(ResourceLifeCycleInterface::class)
        );
    }

    /**
     * Returns the service that retrieves Api Resources.
     *
     * @return ApiResourceRetriever
     */
    public function getResourceRetriever(): ApiResourceRetriever
    {
        if (!$this->retriever) {
            $this->retriever = new ApiResourceRetriever(
                $this->getApiResourceMetadataFactory()
            );
        }
        return $this->retriever;
    }

    /**
     * Returns the service that persist Api Resources.
     *
     * @return ApiResourcePersister
     */
    public function getResourcePersister(): ApiResourcePersister
    {
        if (!$this->persister) {
            $this->persister = new ApiResourcePersister(
                $this->getApiResourceMetadataFactory()
            );
        }
        return $this->persister;
    }

    /**
     * Returns the service that gives the metadata of an api resource.
     *
     * @return ApiResourceMetadataFactory
     */
    public function getApiResourceMetadataFactory(): ApiResourceMetadataFactory
    {
        if (!$this->metadataFactory) {
            $this->metadataFactory = new ApiResourceMetadataFactory(
                $this->apie->getAnnotationReader(),
                $this->apie->getApiResourceFactory()
            );
        }
        return $this->metadataFactory;
    }

    /**
     * Returns the service that extracts identifiers from an api resource.
     *
     * @return IdentifierExtractor
     */
    public function getIdentifierExtractor(): IdentifierExtractor
    {
        if (!$this->identifierExtractor) {
            $this->identifierExtractor = new IdentifierExtractor($this->apie->getObjectAccess());
        }
        return $this->identifierExtractor;
    }

    /**
     * Returns the class that converts from  URL slug to PHP class and viceversa.
     *
     * @return ClassResourceConverter
     */
    public function getClassResourceConverter(): ClassResourceConverter
    {
        return new ClassResourceConverter(
            $this->apie->getPropertyConverter(),
            new ApiResources($this->apie->getResources()),
            $this->apie->isDebug()
        );
    }
}
