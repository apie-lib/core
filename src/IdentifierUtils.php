<?php
namespace Apie\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Serializer\Serializer;
use LogicException;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

final class IdentifierUtils
{
    private function __construct()
    {
    }

    /**
     * @template T of EntityInterface
     * @param ReflectionClass<IdentifierInterface<T>>|IdentifierInterface<T> $identifier
     * @return ReflectionClass<T>
     */
    public static function identifierToEntityClass(ReflectionClass|IdentifierInterface $identifier): ReflectionClass
    {
        if ($identifier instanceof IdentifierInterface) {
            $identifier = new ReflectionClass($identifier);
        }
        return $identifier->getMethod('getReferenceFor')->invoke(null);
    }

    /**
     * @return IdentifierInterface<EntityInterface>
     */
    public static function idStringToIdentifier(string $id, ApieContext $context): IdentifierInterface
    {
        $resourceClass = new ReflectionClass($context->getContext(ContextConstants::RESOURCE_NAME));
        $idClass = self::entityClassToIdentifier($resourceClass);
        /** @var IdentifierInterface<EntityInterface> $idObject */
        $idObject = $context->hasContext(Serializer::class)
            ? $context->getContext(Serializer::class)->denormalizeNewObject($id, $idClass->name, $context)
            : $idClass->newInstance($id);
        return $idObject;
    }

    /**
     * @template T of EntityInterface
     * @param ReflectionClass<T>|T $identifier
     * @return ReflectionClass<IdentifierInterface<T>>
     */
    public static function entityClassToIdentifier(ReflectionClass|EntityInterface $identifier): ReflectionClass
    {
        if ($identifier instanceof EntityInterface) {
            $identifier = new ReflectionClass($identifier);
        }
        $returnType = $identifier->getMethod('getId')->getReturnType();
        if (!($returnType instanceof ReflectionNamedType)) {
            throw new InvalidTypeException($returnType, 'ReflectionNamedType');
        }
        return new ReflectionClass($returnType->getName());
    }

    /**
     * @param ReflectionClass<object> $class
     */
    public static function classNameToUnderscore(ReflectionClass $class): string
    {
        $str = $class->getShortName();
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", '_$0', $str);
        return strtolower($str);
    }

    public static function propertyToUnderscore(ReflectionProperty $property): string
    {
        // TODO check visibility with private properties
        $str = $property->name;
        $str = lcfirst($str);
        $str = preg_replace("/[A-Z]/", '_$0', $str);
        return strtolower($str);
    }

    /**
     * @template T of EntityInterface
     * @param T $entity
     * @param IdentifierInterface<T> $identifier
     */
    public static function injectIdentifier(EntityInterface $entity, IdentifierInterface $identifier): void
    {
        $refl = new ReflectionClass($entity);
        while ($refl) {
            if ($refl->hasProperty('id')) {
                $prop = $refl->getProperty('id');
                $prop->setAccessible(true);
                $prop->setValue($entity, $identifier);
                return;
            }
            $refl = $refl->getParentClass();
        }
        throw new LogicException('I could not find an "id" property!');
    }
}
