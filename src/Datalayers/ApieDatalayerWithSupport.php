<?php
namespace Apie\Core\Datalayers;

use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use ReflectionClass;

interface ApieDataLayerWithSupport
{
    public function isSupported(EntityInterface|ReflectionClass|IdentifierInterface $instance): bool;
}
