<?php

namespace Apie\Core\Models;

use Apie\Core\Interfaces\ResourceSerializerInterface;
use Apie\Core\PluginInterfaces\ResourceLifeCycleInterface;
use Apie\Core\SearchFilters\SearchFilterRequest;
use Psr\Http\Message\ResponseInterface;
use W2w\Lib\Apie\Events\ResponseAllEvent;

/**
 * Data class returned by ApiResourceFacade for the GET resources.
 */
class ApiResourceListFacadeResponse extends ApiResourceFacadeResponse
{
    /**
     * @var string
     */
    private $resourceClass;

    /**
     * @var SearchFilterRequest
     */
    private $filterRequest;

    /**
     * @param ResourceSerializerInterface $serializer
     * @param mixed $resourceList
     * @param string $resourceClass
     * @param SearchFilterRequest $filterRequest
     * @param string|null $acceptHeader
     * @param ResourceLifeCycleInterface[]
     */
    public function __construct(
        ResourceSerializerInterface $serializer,
        iterable $resourceList,
        string $resourceClass,
        SearchFilterRequest $filterRequest,
        ?string $acceptHeader,
        iterable $resourceLifeCycles = []
    ) {
        parent::__construct($serializer, $resourceList, $acceptHeader, $resourceLifeCycles);
        $this->resourceClass = $resourceClass;
        $this->filterRequest = $filterRequest;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        $event = new ResponseAllEvent($this->resourceClass, $this->filterRequest, $this->getResource(), $this->getAcceptHeader());
        $this->runLifeCycleEvent('onPreCreateResponse', $event);
        if (!$event->getResponse()) {
            $event->setResponse($this->getSerializer()->toResponse($this->getResource(), $this->getAcceptHeader()));
        }
        $this->runLifeCycleEvent('onPostCreateResponse', $event);

        return $event->getResponse();
    }
}
