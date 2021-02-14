<?php


namespace Apie\Core\FrameworkBridge;

use Apie\Core\Apie;
use Apie\Core\Exceptions\BadConfigurationException;
use Apie\Core\PluginInterfaces\ApieAwareInterface;
use Apie\Core\PluginInterfaces\ApieAwareTrait;
use Apie\Core\PluginInterfaces\FrameworkConnectionInterface;
use Apie\Core\SearchFilters\SearchFilterRequest;

class FrameworkLessConnection implements FrameworkConnectionInterface, ApieAwareInterface
{
    use ApieAwareTrait;

    public function __construct(Apie $apie)
    {
        $this->setApie($apie);
    }

    public function getService(string $id): object
    {
        throw new BadConfigurationException('No service "' . $id . '" found!');
    }

    public function getUrlForResource(object $resource): ?string
    {
        $classResourceConverter = $this->getApie()->getClassResourceConverter();
        $identifierExtractor = $this->getApie()->getIdentifierExtractor();
        $apiMetadataFactory = $this->getApie()->getApiResourceMetadataFactory();
        $metadata = $apiMetadataFactory->getMetadata($resource);
        $identifier = $identifierExtractor->getIdentifierValue($resource, $metadata->getContext());
        if (!$identifier || !$metadata->allowGet()) {
            return null;
        }
        return $this->getBaseUrl() . '/' . $classResourceConverter->normalize($metadata->getClassName()) . '/' . $identifier;
    }

    public function getExampleUrl(string $resourceClass): ?string
    {
        $url = $this->getOverviewUrlForResourceClass($resourceClass);
        if (null === $url) {
            return null;
        }
        return $url . '/12345';
    }

    public function getOverviewUrlForResourceClass(
        string $resourceClass,
        ?SearchFilterRequest $filterRequest = null
    ): ?string {
        $classResourceConverter = $this->getApie()->getClassResourceConverter();
        $apiMetadataFactory = $this->getApie()->getApiResourceMetadataFactory();
        $metadata = $apiMetadataFactory->getMetadata($resourceClass);
        if (!$metadata->allowGetAll()) {
            return null;
        }
        $query = '';
        if ($filterRequest) {
            $searchQuery = $filterRequest->getSearches();
            $searchQuery['page'] = $filterRequest->getPageIndex();
            $searchQuery['limit'] = $filterRequest->getNumberOfItems();
            $query = '?' . http_build_query($searchQuery);
        }
        return $this->getBaseUrl() . '/' . $classResourceConverter->normalize($metadata->getClassName()) . $query;
    }

    /**
     * Returns base url if one is set up.
     *
     * @return string
     */
    private function getBaseUrl(): string
    {
        try {
            return $this->getApie()->getBaseUrl();
        } catch (BadConfigurationException $exception) {
            return '';
        }
    }

    public function getAcceptLanguage(): ?string
    {
        return null;
    }

    public function getContentLanguage(): ?string
    {
        return null;
    }
}
