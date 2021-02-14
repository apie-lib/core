<?php

namespace Apie\Core;

use Apie\Core\Annotations\ApiResource;
use Apie\Core\Exceptions\ApiResourceAnnotationNotFoundException;
use Apie\Core\Interfaces\ApiResourceFactoryInterface;
use Apie\Core\Models\ApiResourceClassMetadata;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;

/**
 * Creates Api Resource metadata using annotations on the class.
 */
class ApiResourceMetadataFactory
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var ApiResourceFactoryInterface
     */
    private $retrieverFactory;

    public function __construct(Reader $reader, ApiResourceFactoryInterface $retrieverFactory)
    {
        $this->reader = $reader;
        $this->retrieverFactory = $retrieverFactory;
    }

    public function getMetadata($classNameOrInstance): ApiResourceClassMetadata
    {
        $reflClass = new ReflectionClass($classNameOrInstance);
        $annotation = $this->reader->getClassAnnotation(
            $reflClass,
            ApiResource::class
        );
        if (!$annotation) {
            throw new ApiResourceAnnotationNotFoundException($classNameOrInstance);
        }
        /** @var ApiResource $annotation */
        $retriever = null;
        $persister = null;
        if ($annotation->retrieveClass) {
            $retriever = $this->retrieverFactory->getApiResourceRetrieverInstance($annotation->retrieveClass);
        }
        if ($annotation->persistClass) {
            $persister = $this->retrieverFactory->getApiResourcePersisterInstance($annotation->persistClass);
        }

        return new ApiResourceClassMetadata($reflClass->getName(), $annotation, $retriever, $persister);
    }
}
