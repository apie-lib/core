<?php

namespace Apie\Core;

use Apie\Core\Encodings\ChainableFormatRetriever;
use Apie\Core\Exceptions\BadConfigurationException;
use Apie\Core\FrameworkBridge\ChainedFrameworkConnection;
use Apie\Core\FrameworkBridge\FrameworkLessConnection;
use Apie\Core\Interfaces\ApiResourceFactoryInterface;
use Apie\Core\Interfaces\FormatRetrieverInterface;
use Apie\Core\Interfaces\ResourceSerializerInterface;
use Apie\Core\Internal\ApieCore;
use Apie\Core\Internal\PluginContainer;
use Apie\Core\PluginInterfaces\AnnotationReaderProviderInterface;
use Apie\Core\PluginInterfaces\ApieAwareInterface;
use Apie\Core\PluginInterfaces\ApieConfigInterface;
use Apie\Core\PluginInterfaces\ApiResourceFactoryProviderInterface;
use Apie\Core\PluginInterfaces\CacheItemPoolProviderInterface;
use Apie\Core\PluginInterfaces\EncoderProviderInterface;
use Apie\Core\PluginInterfaces\FrameworkConnectionInterface;
use Apie\Core\PluginInterfaces\NormalizerProviderInterface;
use Apie\Core\PluginInterfaces\ObjectAccessProviderInterface;
use Apie\Core\PluginInterfaces\OpenApiEventProviderInterface;
use Apie\Core\PluginInterfaces\OpenApiInfoProviderInterface;
use Apie\Core\PluginInterfaces\ResourceProviderInterface;
use Apie\Core\PluginInterfaces\SchemaProviderInterface;
use Apie\Core\PluginInterfaces\SerializerProviderInterface;
use Apie\Core\PluginInterfaces\SymfonyComponentProviderInterface;
use Apie\Core\ResourceFactories\ChainableFactory;
use Apie\Core\SearchFilters\SearchFilterRequest;
use Apie\ObjectAccessNormalizer\ObjectAccess\CachedObjectAccess;
use Apie\ObjectAccessNormalizer\ObjectAccess\GroupedObjectAccess;
use Apie\ObjectAccessNormalizer\ObjectAccess\ObjectAccess;
use Apie\ObjectAccessNormalizer\ObjectAccess\ObjectAccessInterface;
use Apie\OpenapiSchema\Contract\InfoContract;
use Apie\OpenapiSchema\Spec\Document;
use Apie\OpenapiSchema\Spec\Info;
use Doctrine\Common\Annotations\Reader;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use W2w\Lib\Apie\Core\IdentifierExtractor;
use W2w\Lib\Apie\Plugins\Core\CorePlugin;
use W2w\Lib\Apie\Plugins\PrimaryKey\PrimaryKeyPlugin;

final class Apie implements
    SerializerProviderInterface,
    ResourceProviderInterface,
    NormalizerProviderInterface,
    EncoderProviderInterface,
    SymfonyComponentProviderInterface,
    CacheItemPoolProviderInterface,
    AnnotationReaderProviderInterface,
    ApiResourceFactoryProviderInterface,
    OpenApiInfoProviderInterface,
    ApieConfigInterface,
    SchemaProviderInterface,
    OpenApiEventProviderInterface,
    FrameworkConnectionInterface
{
    const VERSION = "1.0";

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var string|null
     */
    private $cacheFolder;

    /**
     * @var PluginContainer
     */
    private $pluginContainer;

    /**
     * @var ApieCore
     */
    private $apieCore;

    /**
     * @var ChainableFactory|null
     */
    private $chainableFactory;

    /**
     * @var ChainableFormatRetriever|null
     */
    private $chainableFormatRetriever;

    /**

     * @param object[] $plugins
     * @param bool $debug
     * @param string|null $cacheFolder
     * @param bool $addCorePlugin
     */
    public function __construct(array $plugins, bool $debug = false, ?string $cacheFolder = null, bool $addCorePlugin = true)
    {
        $this->debug = $debug;
        $this->cacheFolder = $cacheFolder;
        if ($addCorePlugin) {
            if (class_exists(PrimaryKeyPlugin::class)) {
                $plugins[] = new PrimaryKeyPlugin();
            }
            if (class_exists(CorePlugin::class)) {
                $plugins[] = new CorePlugin();
            }
        }
        $this->pluginContainer = new PluginContainer($plugins);
        $this->pluginContainer->each(
            ApieAwareInterface::class,
            function (ApieAwareInterface $plugin) {
                $plugin->setApie($this);
            }
        );
        $this->apieCore = new ApieCore($this, $this->pluginContainer);
    }

    /**
     * Creates a new instance of Apie reusing the services/instances of the current Apie instance.
     *
     * @param array $plugins
     * @return Apie
     */
    public function createContext(array $plugins = []): self
    {
        if (class_exists(PrimaryKeyPlugin::class)) {
            $plugins[] = new PrimaryKeyPlugin();
        }
        $plugins[] = $this;
        return new Apie($plugins, $this->debug, $this->cacheFolder, false);
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @return string|null
     */
    public function getCacheFolder(): ?string
    {
        return $this->cacheFolder;
    }

    public function getPlugin(string $pluginClass): object
    {
        return $this->pluginContainer->getPlugin($pluginClass);
    }

    /**
     * @return ResourceSerializerInterface
     */
    public function getResourceSerializer(): ResourceSerializerInterface
    {
        $serializer = $this->pluginContainer->first(
            SerializerProviderInterface::class,
            new BadConfigurationException('I have no resource serializer set up')
        )->getResourceSerializer();
        return $serializer;
    }

    /**
     * Returns a list of Api resources.
     *
     * @return string[]
     */
    public function getResources(): array
    {
        return array_values(array_unique($this->pluginContainer->merge(ResourceProviderInterface::class, 'getResources')));
    }

    /**
     * @return NormalizerInterface[]|DenormalizerInterface[]
     */
    public function getNormalizers(): array
    {
        return $this->pluginContainer->merge(NormalizerProviderInterface::class, 'getNormalizers');
    }

    /**
     * @return EncoderInterface[]|DecoderInterface[]
     */
    public function getEncoders(): array
    {
        return $this->pluginContainer->merge(EncoderProviderInterface::class, 'getEncoders');
    }

    /**
     * @param string $interface
     * @return mixed[]
     */
    public function getPluginsWithInterface(string $interface): array
    {
        return $this->pluginContainer->getPluginsWithInterface($interface);
    }

    public function getClassMetadataFactory(): ClassMetadataFactoryInterface
    {
        return $this->pluginContainer->first(
            SymfonyComponentProviderInterface::class,
            new BadConfigurationException('I have no symfony component provider set up')
        )->getClassMetadataFactory();
    }

    public function getPropertyConverter(): NameConverterInterface
    {
        return $this->pluginContainer->first(
            SymfonyComponentProviderInterface::class,
            new BadConfigurationException('I have no symfony component provider set up')
        )->getPropertyConverter();
    }

    public function getCacheItemPool(): CacheItemPoolInterface
    {
        return $this->pluginContainer->first(
            CacheItemPoolProviderInterface::class,
            new BadConfigurationException('I have no cache item pool provider set up')
        )->getCacheItemPool();
    }

    public function getAnnotationReader(): Reader
    {
        return $this->pluginContainer->first(
            AnnotationReaderProviderInterface::class,
            new BadConfigurationException('I have no annotation reader set up')
        )->getAnnotationReader();
    }

    public function getFormatRetriever(): FormatRetrieverInterface
    {
        if (!$this->chainableFormatRetriever) {
            $this->chainableFormatRetriever = new ChainableFormatRetriever(
                iterator_to_array($this->pluginContainer->combine(EncoderProviderInterface::class, 'getFormatRetriever'))
            );
        }
        return $this->chainableFormatRetriever;
    }

    public function getIdentifierExtractor(): IdentifierExtractor
    {
        return $this->apieCore->getIdentifierExtractor();
    }

    public function getApiResourceMetadataFactory(): ApiResourceMetadataFactory
    {
        return $this->apieCore->getApiResourceMetadataFactory();
    }

    public function getApiResourceFacade(): ApiResourceFacade
    {
        return $this->apieCore->getApiResourceFacade();
    }

    public function getOpenApiSpecGenerator(): OpenApiSpecGenerator
    {
        return $this->apieCore->getOpenApiSpecGenerator();
    }

    public function getSchemaGenerator(): OpenApiSchemaGenerator
    {
        return $this->apieCore->getSchemaGenerator();
    }

    public function getApiResourceFactory(): ApiResourceFactoryInterface
    {
        if (!$this->chainableFactory) {
            $this->chainableFactory = new ChainableFactory(
                iterator_to_array($this->pluginContainer->combine(ApiResourceFactoryProviderInterface::class, 'getApiResourceFactory'))
            );
        }
        return $this->chainableFactory;
    }

    public function createInfo(): InfoContract
    {
        $res = $this->pluginContainer->first(OpenApiInfoProviderInterface::class, null);

        if (empty($res)) {
            return new Info('Apie', Apie::VERSION);
        }
        return $res->createInfo();
    }

    public function getBaseUrl(): string
    {
        return $this->pluginContainer->first(ApieConfigInterface::class, new BadConfigurationException('I have no config set up'))->getBaseUrl();
    }

    public function getDefinedStaticData(): array
    {
        $result = [];
        foreach (array_reverse($this->pluginContainer->getPluginsWithInterface(SchemaProviderInterface::class)) as $schemaDefinition) {
            foreach ($schemaDefinition->getDefinedStaticData() as $className => $schema) {
                $result[$className] = $schema;
            }
        }
        return $result;
    }

    public function getDynamicSchemaLogic(): array
    {
        $result = [];
        foreach (array_reverse($this->pluginContainer->getPluginsWithInterface(SchemaProviderInterface::class)) as $schemaDefinition) {
            foreach ($schemaDefinition->getDynamicSchemaLogic() as $className => $callable) {
                $result[$className] = $callable;
            }
        }
        return $result;
    }

    public function getClassResourceConverter(): ClassResourceConverter
    {
        return $this->apieCore->getClassResourceConverter();
    }

    public function onOpenApiDocGenerated(Document $document): Document
    {
        $this->pluginContainer->each(OpenApiEventProviderInterface::class, function (OpenApiEventProviderInterface $plugin) use (&$document) {
            $document = $plugin->onOpenApiDocGenerated($document);
        });
        return $document;
    }

    public function getObjectAccess(): ObjectAccessInterface
    {
        $objectAccess = new ObjectAccess();
        $objectAccesses = $this->pluginContainer->getPluginsWithInterface(ObjectAccessProviderInterface::class);
        if (!empty($objectAccesses)) {
            $list = [];
            foreach ($objectAccesses as $objectAccessPlugin) {
                $list = array_merge($list, $objectAccessPlugin->getObjectAccesses());
            }
            $objectAccess = new GroupedObjectAccess($objectAccess, $list);
        }
        if (!$this->debug && $this->cacheFolder) {
            return new CachedObjectAccess($objectAccess, $this->getCacheItemPool());
        }
        return $objectAccess;
    }

    public function getFrameworkConnection(): FrameworkConnectionInterface
    {
        $res = new ChainedFrameworkConnection(
            $this->getPluginsWithInterface(FrameworkConnectionInterface::class),
            new FrameworkLessConnection($this)
        );
        return $res;
    }

    public function getService(string $id): object
    {
        return $this->getFrameworkConnection()->getService($id);
    }

    public function getUrlForResource(object $resource): ?string
    {
        return $this->getFrameworkConnection()->getUrlForResource($resource);
    }

    public function getOverviewUrlForResourceClass(
        string $resourceClass,
        ?SearchFilterRequest $filterRequest = null
    ): ?string {
        return $this->getFrameworkConnection()->getOverviewUrlForResourceClass($resourceClass, $filterRequest);
    }

    public function getAcceptLanguage(): ?string
    {
        return $this->getFrameworkConnection()->getAcceptLanguage();
    }

    public function getContentLanguage(): ?string
    {
        return $this->getFrameworkConnection()->getContentLanguage();
    }

    public function getExampleUrl(string $resourceClass): ?string
    {
        return $this->getFrameworkConnection()->getExampleUrl($resourceClass);
    }
}
