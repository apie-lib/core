<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\ApieLib;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Lists\ReflectionClassList;
use Apie\Core\Lists\ReflectionMethodList;
use Apie\Core\Utils\ConverterUtils;
use ReflectionClass;
use ReflectionNamedType;

final class BoundedContext implements EntityInterface
{
    public readonly BoundedContextId $id;

    public function __construct(
        BoundedContextId|string $id,
        public readonly ReflectionClassList $resources,
        public readonly ReflectionMethodList $actions,
    ) {
        $this->id = $id instanceof BoundedContextId ? $id : new BoundedContextId($id);
    }

    /**
     * @param ReflectionClass<object> $resourceClass
     */
    public function contains(ReflectionClass $resourceClass): bool
    {
        foreach ($this->resources as $resource) {
            if ($resource->name === $resourceClass->name) {
                return true;
            }
        }

        return false;
    }

    public function findRelatedClasses(): ReflectionClassList
    {
        $list = [];
        foreach ($this->resources as $resource) {
            $list[$resource->name] = true;
            // TODO: check classes
        }
        foreach ($this->actions as $action) {
            $list[$action->getDeclaringClass()->name] = true;
            $class = ConverterUtils::toReflectionClass($action->getReturnType());
            if ($class !== null) {
                $list[$class->name] = true;
                if (ApieLib::hasAlias($class->name)) {
                    $type = ConverterUtils::toReflectionType(
                        ApieLib::getAlias($class->name)
                    );
                    // @phpstan-ignore method.notFound
                    $types = ($type instanceof ReflectionNamedType) ? [$type] : $type->getTypes();
                    foreach ($types as $type) {
                        $class = ConverterUtils::toReflectionClass($type);
                        if ($class !== null) {
                            $list[$class->name] = true;
                        }
                    }
                
                }
            }
        }
        return new ReflectionClassList(
            array_map(
                function (string $class) {
                    return new ReflectionClass($class);
                },
                array_keys($list)
            )
        );
    }

    public function getId(): BoundedContextId
    {
        return $this->id;
    }
}
