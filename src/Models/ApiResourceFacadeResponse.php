<?php

namespace Apie\Core\Models;

use Apie\Core\Apie;
use Apie\Core\Events\NormalizeEvent;
use Apie\Core\Events\ResponseEvent;
use Apie\Core\Interfaces\ResourceSerializerInterface;
use Apie\Core\PluginInterfaces\ResourceLifeCycleInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Data class returned by ApiResourceFacade.
 */
class ApiResourceFacadeResponse
{
    /**
     * @var ResourceSerializerInterface
     */
    private $serializer;

    /**
     * @var mixed
     */
    private $resource;

    /**
     * @var string|null
     */
    private $acceptHeader;

    /**
     * @var iterable<ResourceLifeCycleInterface>
     */
    private $resourceLifeCycles;

    /**
     * @param ResourceSerializerInterface $serializer
     * @param mixed $resource
     * @param string|null $acceptHeader
     * @param ResourceLifeCycleInterface[]
     */
    public function __construct(
        ResourceSerializerInterface $serializer,
        $resource,
        ?string $acceptHeader,
        iterable $resourceLifeCycles = []
    ) {
        $this->serializer = $serializer;
        $this->resource = $resource;
        $this->acceptHeader = $acceptHeader;
        $this->resourceLifeCycles = $resourceLifeCycles;
    }

    /**
     * Helper method to call the method on all all lifecycle instances.
     *
     * @param string $event
     * @param mixed[] $args
     */
    protected function runLifeCycleEvent(string $event, ...$args)
    {
        foreach ($this->resourceLifeCycles as $resourceLifeCycle) {
            $resourceLifeCycle->$event(...$args);
        }
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        $event = new ResponseEvent($this->resource, $this->acceptHeader ?? 'application/json');
        $this->runLifeCycleEvent('onPreCreateResponse', $event);
        if (!$event->getResponse()) {
            $event->setResponse(
                $this->serializer->toResponse($this->resource, $this->getAcceptHeader())
                    ->withHeader('x-apie', Apie::VERSION)
            );
        }
        $this->runLifeCycleEvent('onPostCreateResponse', $event);

        return $event->getResponse();
    }

    /**
     * Gets data the way we would send it normalized.
     *
     * @return mixed
     */
    public function getNormalizedData()
    {
        $event = new NormalizeEvent($this->resource, $this->acceptHeader ?? 'application/json');
        $this->runLifeCycleEvent('onPreCreateNormalizedData', $event);
        if (!$event->hasNormalizedData()) {
            $event->setNormalizedData($this->serializer->normalize($this->resource, $this->getAcceptHeader()));
        }
        $this->runLifeCycleEvent('onPostCreateNormalizedData', $event);
        return $event->getNormalizedData();
    }

    /**
     * Get the accept header.
     *
     * @return string|null
     */
    public function getAcceptHeader(): ?string
    {
        return $this->acceptHeader ?? 'application/json';
    }

    /**
     * Get the serializer.
     *
     * @return ResourceSerializerInterface
     */
    protected function getSerializer(): ResourceSerializerInterface
    {
        return $this->serializer;
    }
}
