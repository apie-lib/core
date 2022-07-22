<?php
namespace Apie\Core\Actions;

use Apie\Core\Context\ApieContext;
use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;
use Psr\Http\Message\ResponseInterface;

interface HasRouteDefinition
{
    public function getMethod(): RequestMethod;
    public function getUrl(): UrlRouteDefinition;
    public function toResponse(ApieContext $context): ResponseInterface;
}
