<?php
namespace Apie\Core;

use Apie\ServiceProviderGenerator\UseGeneratedMethods;
use Illuminate\Support\ServiceProvider;

/**
 * This file is generated with apie/service-provider-generator from file: core.yaml
 * @codecoverageIgnore
 * @phpstan-ignore
 */
class CoreServiceProvider extends ServiceProvider
{
    use UseGeneratedMethods;

    public function register()
    {
        $this->app->singleton(
            'apie.route_definitions.provider',
            function ($app) {
                return \Apie\Common\Wrappers\GeneralServiceFactory::createRoutedDefinitionProvider(
                    $this->getTaggedServicesIterator('apie.core.route_definition')
                );
                
            }
        );
        $this->app->bind('apie.csrf_token_provider', \Apie\Core\Session\CsrfTokenProvider::class);
        
        $this->app->bind(\Apie\Core\Session\CsrfTokenProvider::class, \Apie\ApieBundle\ContextBuilders\CsrfTokenContextBuilder::class);
        
        $this->app->singleton(
            \Apie\Core\BoundedContext\BoundedContextHashmap::class,
            function ($app) {
                return $this->app->make('apie.bounded_context.hashmap_factory')->create(
                
                );
                
            }
        );
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
                    $app->make(\Apie\Core\Datalayers\Grouped\DataLayerByBoundedContext::class)
                );
            }
        );
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
