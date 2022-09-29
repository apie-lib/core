<?php
namespace Apie\Core\Datalayers\Grouped;

use Apie\Core\Datalayers\ApieDatalayer;
use Apie\Core\Exceptions\ObjectIsImmutable;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ItemHashmap;
use ReflectionClass;

final class DataLayerByClass extends ItemHashmap
{
    private ApieDataLayer $defaultDataLayer;

    public function offsetGet(mixed $offset): ApieDatalayer
    {
        return parent::offsetGet($offset);
    }

    public function setDefaultDataLayer(ApieDatalayer $defaultDataLayer): self
    {
        if (isset($this->defaultDataLayer)) {
            throw new ObjectIsImmutable($this);
        }
        $this->mutable = false;
        $this->defaultDataLayer = $defaultDataLayer;
        return $this;
    }

    /**
     * @param ReflectionClass<EntityInterface> $class
     */
    public function pickDataLayerFor(ReflectionClass $class): ApieDatalayer
    {
        $className = $class->name;
        return $this[$className] ?? $this->defaultDataLayer;
    }
}
