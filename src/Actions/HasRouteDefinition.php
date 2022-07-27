<?php
namespace Apie\Core\Actions;

use Apie\Core\Enums\RequestMethod;
use Apie\Core\ValueObjects\UrlRouteDefinition;

interface HasRouteDefinition
{
    public function getMethod(): RequestMethod;
    public function getUrl(): UrlRouteDefinition;
    public function getController(): string;
    public function getRouteAttributes(): array;
}
