<?php
namespace Apie\Core;

use Apie\Core\Context\ApieContext;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Serializer\Serializer;
use Exception;
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
     * @param bool $throwError
     * @phpstan-return ($throwError is true ? IdentifierInterface<EntityInterface> : IdentifierInterface<EntityInterface>|null)
     */
    public static function idStringToIdentifier(
        string $id,
        ApieContext $context,
        bool $throwError = true
    ): ?IdentifierInterface {
        $resourceName = $context->getContext(ContextConstants::RESOURCE_NAME, $throwError);
        if (empty($resourceName) && !$throwError) {
            return null;
        }
        try {
            $resourceClass = new ReflectionClass($resourceName ?? '');
            $idClass = self::entityClassToIdentifier($resourceClass);
            /** @var IdentifierInterface<EntityInterface> $idObject */
            $idObject = $context->hasContext(Serializer::class)
                ? $context->getContext(Serializer::class)->denormalizeNewObject($id, $idClass->name, $context)
                : $idClass->newInstance($id);
            return $idObject;
        } catch (Exception $exception) {
            if ($throwError) {
                throw $exception;
            }
            return null;
        }
    }

    /**
     * @template T of EntityInterface
     * @param ReflectionClass<covariant T>|T $identifier
     * @return ReflectionClass<covariant IdentifierInterface<T>>
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
     * @param ReflectionClass<covariant object> $class
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
                $prop->setValue($entity, $identifier);
                return;
            }
            $refl = $refl->getParentClass();
        }
        throw new LogicException('I could not find an "id" property!');
    }
}
