<?php
namespace Apie\Core\Datalayers\Grouped;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Core\Lists\ItemHashmap;
use ReflectionClass;

final class DataLayerByBoundedContext extends ItemHashmap
{
    private ApieDataLayer|DataLayerByClass $defaultDataLayer;

    public function offsetGet(mixed $offset): DataLayerByClass
    {
        return parent::offsetGet($offset);
    }

    public function setDefaultDataLayer(ApieDatalayer|DataLayerByClass $defaultDataLayer): self
    {
        if (isset($this->defaultDataLayer)) {
            throw new ObjectIsImmutable($this);
        }
        $this->mutable = false;
        $this->defaultDataLayer = $defaultDataLayer;
        return $this;
    }

    public function pickDataLayerFor(ReflectionClass $class, BoundedContextId $boundedContextId): ApieDatalayer
    {
        if (isset($this[$boundedContextId->toNative()])) {
            return $this[$boundedContextId->toNative()]->pickDataLayerFor($class);
        }
        if ($this->defaultDataLayer instanceof DataLayerByClass) {
            return $this->defaultDataLayer->pickDataLayerFor($class);
        }
        return $this->defaultDataLayer;
    }
}
