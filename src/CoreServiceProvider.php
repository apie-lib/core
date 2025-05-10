<?php
namespace Apie\Core;

use Apie\ServiceProviderGenerator\UseGeneratedMethods;
use Illuminate\Support\ServiceProvider;

/**
 * This file is generated with apie/service-provider-generator from file: core.yaml
 * @codeCoverageIgnore
 */
class CoreServiceProvider extends ServiceProvider
{
    use UseGeneratedMethods;

    public function register()
    {
        $this->app->bind('apie.csrf_token_provider', \Apie\Core\Session\CsrfTokenProvider::class);
        
        $this->app->singleton(
            \Apie\Core\BoundedContext\BoundedContextHashmap::class,
            function ($app) {
                return $this->app->make('apie.bounded_context.hashmap_factory')->create(
                
                );
                
            }
        );
        $this->app->singleton(
            \Apie\Core\Translator\ApieTranslatorInterface::class,
            function ($app) {
                return \Apie\Core\Translator\ApieTranslator::create(
                
                );
                
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Core\Translator\ApieTranslatorInterface::class,
            array(
              0 => 'apie.context',
            )
        );
        $this->app->tag([\Apie\Core\Translator\ApieTranslatorInterface::class], 'apie.context');
        $this->app->singleton(
            \Apie\Core\ContextBuilders\ContextBuilderFactory::class,
            function ($app) {
                return \Apie\Common\Wrappers\GeneralServiceFactory::createContextBuilderFactory(
                    $app->make(\Apie\Core\BoundedContext\BoundedContextHashmap::class),
                    $app->bound(\Apie\Serializer\DecoderHashmap::class) ? $app->make(\Apie\Serializer\DecoderHashmap::class) : null,
                    $this->getTaggedServicesIterator('apie.core.context_builder')
                );
                
            }
        );
        $this->app->bind('apie.bounded_context.hashmap', \Apie\Core\BoundedContext\BoundedContextHashmap::class);
        
        $this->app->bind('apie.context.factory', \Apie\Core\ContextBuilders\ContextBuilderFactory::class);
        
        $this->app->bind(\Apie\Core\Datalayers\ApieDatalayer::class, \Apie\Core\Datalayers\GroupedDataLayer::class);
        
        $this->app->singleton(
            \Apie\Core\Datalayers\GroupedDataLayer::class,
            function ($app) {
                return new \Apie\Core\Datalayers\GroupedDataLayer(
                    $app->make(\Apie\Core\Datalayers\Grouped\DataLayerByBoundedContext::class),
                    $app->make(\Psr\EventDispatcher\EventDispatcherInterface::class)
                );
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Core\Datalayers\GroupedDataLayer::class,
            array(
              0 => 'apie.context',
            )
        );
        $this->app->tag([\Apie\Core\Datalayers\GroupedDataLayer::class], 'apie.context');
        $this->app->bind('apie.datalayer', \Apie\Core\Datalayers\ApieDatalayer::class);
        
        $this->app->singleton(
            \Apie\Core\Indexing\Indexer::class,
            function ($app) {
                return \Apie\Core\Indexing\Indexer::create(
                
                );
                
            }
        );
        $this->app->singleton(
            \Apie\Core\Datalayers\Search\LazyLoadedListFilterer::class,
            function ($app) {
                return new \Apie\Core\Datalayers\Search\LazyLoadedListFilterer(
                    $app->make(\Apie\Core\Indexing\Indexer::class)
                );
            }
        );
        $this->app->singleton(
            \Apie\Core\Other\FileWriterInterface::class,
            function ($app) {
                return new \Apie\Core\Other\ActualFileWriter(
                
                );
            }
        );
        $this->app->singleton(
            \Apie\Core\FileStorage\ChainedFileStorage::class,
            function ($app) {
                return \Apie\Core\FileStorage\FileStorageFactory::create(
                    $this->parseArgument('%apie.storage%')
                );
                
            }
        );
        \Apie\ServiceProviderGenerator\TagMap::register(
            $this->app,
            \Apie\Core\FileStorage\ChainedFileStorage::class,
            array(
              0 => 'apie.context',
            )
        );
        $this->app->tag([\Apie\Core\FileStorage\ChainedFileStorage::class], 'apie.context');
        $this->app->singleton(
            \Apie\Core\Datalayers\Grouped\DataLayerByBoundedContext::class,
            function ($app) {
                return \Apie\Common\Wrappers\GeneralServiceFactory::createDataLayerMap(
                    $this->parseArgument('%apie.datalayers%'),
                    $this->getTaggedServicesServiceLocator('apie.datalayer')
                );
                
            }
        );
        
    }
}
