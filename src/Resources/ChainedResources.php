<?php
namespace Apie\Core\Resources;

use Apie\Core\Exceptions\BadConfigurationException;

/**
 * Merge multiple ApiResourcesInterface results in one.
 */
class ChainedResources implements ApiResourcesInterface
{
    /**
     * @var string[]
     */
    private $static = [];

    /**
     * @var ApiResourcesInterface[]
     */
    private $chainedResources = [];

    /**
     * @param (string|ApiResourcesInterface)[] $resources
     */
    public function __construct(array $resources)
    {
        foreach ($resources as $resource) {
            if (gettype($resource) === 'string' && class_exists($resource)) {
                $this->static[] = $resource;
                continue;
            }
            if ($resource instanceof ApiResourcesInterface) {
                $this->chainedResources[] = $resource;
                continue;
            }
            throw new BadConfigurationException('I expect to get a list of classes or ApiResourcesInterface instances.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getApiResources(): array
    {
        $res = $this->static;
        foreach ($this->chainedResources as $chainedResource) {
            $res = array_merge($res, $chainedResource->getApiResources());
        }
        return array_values(array_unique($res));
    }
}
