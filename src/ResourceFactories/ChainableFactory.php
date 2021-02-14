<?php


namespace Apie\Core\ResourceFactories;

use Apie\Core\Exceptions\CouldNotConstructApiResourceClassException;
use Apie\Core\Interfaces\ApiResourceFactoryInterface;
use Apie\Core\Interfaces\ApiResourcePersisterInterface;
use Apie\Core\Interfaces\ApiResourceRetrieverInterface;

/**
 * Groups multiple resource factories together,
 */
class ChainableFactory implements ApiResourceFactoryInterface
{
    /**
     * @var ApiResourceRetrieverInterface[]
     */
    private $instantiatedRetrievers = [];

    /**
     * @var ApiResourcePersisterInterface[]
     */
    private $instantiatedPersisters = [];

    /**
     * @var ApiResourceFactoryInterface[]
     */
    private $factories;

    /**
     * @var ApiResourceFactoryInterface[]
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritDoc}
     */
    public function hasApiResourceRetrieverInstance(string $identifier): bool
    {
        if (isset($this->instantiatedRetrievers[$identifier])) {
            return true;
        }
        foreach ($this->factories as $factory) {
            if ($factory->hasApiResourceRetrieverInstance($identifier)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiResourceRetrieverInstance(string $identifier): ApiResourceRetrieverInterface
    {
        if (isset($this->instantiatedRetrievers[$identifier])) {
            return $this->instantiatedRetrievers[$identifier];
        }
        foreach ($this->factories as $factory) {
            if ($factory->hasApiResourceRetrieverInstance($identifier)) {
                $res = $factory->getApiResourceRetrieverInstance($identifier);
                if (get_class($res) === $identifier && $res instanceof ApiResourcePersisterInterface) {
                    $this->instantiatedPersisters[$identifier] = $res;
                }
                return $this->instantiatedRetrievers[$identifier] = $res;
            }
        }
        throw new CouldNotConstructApiResourceClassException($identifier);
    }

    /**
     * {@inheritDoc}
     */
    public function hasApiResourcePersisterInstance(string $identifier): bool
    {
        if (isset($this->instantiatedPersisters[$identifier])) {
            return true;
        }
        foreach ($this->factories as $factory) {
            if ($factory->hasApiResourcePersisterInstance($identifier)) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getApiResourcePersisterInstance(string $identifier): ApiResourcePersisterInterface
    {
        if (isset($this->instantiatedPersisters[$identifier])) {
            return $this->instantiatedPersisters[$identifier];
        }
        foreach ($this->factories as $factory) {
            if ($factory->hasApiResourcePersisterInstance($identifier)) {
                $res = $factory->getApiResourcePersisterInstance($identifier);
                if (get_class($res) === $identifier && $res instanceof ApiResourceRetrieverInterface) {
                    $this->instantiatedRetrievers[$identifier] = $res;
                }
                return $this->instantiatedPersisters[$identifier] = $res;
            }
        }
        throw new CouldNotConstructApiResourceClassException($identifier);
    }
}
