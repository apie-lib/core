<?php


namespace Apie\Tests\Core;

use Apie\Core\Annotations\ApiResource;
use Apie\Core\Apie;
use Apie\Core\ApiResourceFacade;
use Apie\Core\Exceptions\BadConfigurationException;
use Apie\Core\Exceptions\NotAnApiePluginException;
use Apie\CorePlugin\DataLayers\NullDataLayer;
use Apie\CorePlugin\ResourceSerializers\SymfonySerializerAdapter;
use Apie\FakeAnnotationsPlugin\FakeAnnotationsPlugin;
use Apie\MockObjects\ApiResources\SimplePopo;
use Apie\ObjectAccessNormalizer\ObjectAccess\GroupedObjectAccess;
use Apie\OpenapiSchema\Spec\Info;
use Apie\StaticConfigPlugin\StaticConfigPlugin;
use Apie\StaticResourcesPlugin\StaticResourcesPlugin;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;

class ApieTest extends TestCase
{
    public function test_invalid_plugin_throws_exception()
    {
        $this->expectException(NotAnApiePluginException::class);
        new Apie([$this], true, null);
    }

    /**
     * @dataProvider noPluginsThrowExceptionsProvider
     */
    public function test_no_plugins_throw_exceptions(string $expectedException, string $method)
    {
        $testItem = new Apie([], true, null, false);
        $this->assertEquals(true, $testItem->isDebug());
        $this->expectException($expectedException);
        $testItem->$method();
    }

    public function noPluginsThrowExceptionsProvider()
    {
        yield [BadConfigurationException::class, 'getResourceSerializer'];
        yield [BadConfigurationException::class, 'getClassMetadataFactory'];
        yield [BadConfigurationException::class, 'getPropertyConverter'];
        yield [BadConfigurationException::class, 'getCacheItemPool'];
        yield [BadConfigurationException::class, 'getAnnotationReader'];
        yield [BadConfigurationException::class, 'getResourceSerializer'];
        yield [BadConfigurationException::class, 'getApiResourceFacade'];
        yield [BadConfigurationException::class, 'getOpenApiSpecGenerator'];
        yield [BadConfigurationException::class, 'getBaseUrl'];
    }

    public function test_getService_throws_exception()
    {
        $testItem = new Apie([], true, null);
        $this->expectException(BadConfigurationException::class);
        $testItem->getService(Apie::class);
    }

    public function test_framework_connection_works_without_override()
    {
        $testItem = new Apie(
            [
                new StaticResourcesPlugin([SimplePopo::class]),
                new FakeAnnotationsPlugin(
                    [
                        SimplePopo::class => ApiResource::createFromArray(['retrieveClass' => NullDataLayer::class]),
                    ]
                ),
            ],
            true,
            null
        );
        $this->assertNull($testItem->getAcceptLanguage());
        $this->assertNull($testItem->getContentLanguage());
        $this->assertEquals('/simple_popo/12345', $testItem->getExampleUrl(SimplePopo::class));
        $this->assertEquals('/simple_popo', $testItem->getOverviewUrlForResourceClass(SimplePopo::class));
        srand(0);
        $this->assertEquals('/simple_popo/QPBQZRDZRRZYQVKA', $testItem->getUrlForResource(new SimplePopo()));
    }

    /**
     * @dataProvider corePluginNoExceptionProvider
     */
    public function test_core_plugin_No_exception(string $expectedClass, string $method)
    {
        $testItem = new Apie([new StaticConfigPlugin('')], true, null);
        $this->assertEquals(true, $testItem->isDebug());
        $this->assertEquals($expectedClass, get_class($testItem->$method()));
    }

    public function corePluginNoExceptionProvider()
    {
        yield [SymfonySerializerAdapter::class, 'getResourceSerializer'];
        yield [ClassMetadataFactory::class, 'getClassMetadataFactory'];
        yield [MetadataAwareNameConverter::class, 'getPropertyConverter'];
        yield [GroupedObjectAccess::class, 'getObjectAccess'];
        yield [ArrayAdapter::class, 'getCacheItemPool'];
        yield [AnnotationReader::class, 'getAnnotationReader'];
        yield [SymfonySerializerAdapter::class, 'getResourceSerializer'];
        yield [ApiResourceFacade::class, 'getApiResourceFacade'];
        yield [Info::class, 'createInfo'];
        yield [OpenApiSpecGenerator::class, 'getOpenApiSpecGenerator'];
    }
}
