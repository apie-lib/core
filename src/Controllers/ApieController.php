<?php
namespace Apie\Core\Controllers;

use Apie\Core\Actions\ActionInterface;
use Apie\Core\Actions\HasRouteDefinition;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApieController
{
    public function __construct(
        private ActionInterface&HasRouteDefinition $action,
        private ContextBuilderFactory $contextBuilderFactory
    ) {
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        $context = $this->contextBuilderFactory->createFromRequest($request)
            ->registerInstance($this->action);
        $context = $this->action->process($context);
        return $this->action->toResponse($context);
    }
}
