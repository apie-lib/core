<?php

namespace Apie\Core\ValueObjects;

use Apie\Core\ApieLib;
use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\FakeMethod;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\IdentifierUtils;
use Apie\Core\RegexUtils;
use Apie\Core\Utils\ConverterUtils;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\TypeConverter\ReflectionTypeFactory;
use Faker\Generator;

#[FakeMethod('createRandom')]
#[Description('URI to a different resource without bounded context, for example "Resource/123"')]
class IdentifierUri implements HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public static function validate(string $input): void
    {
        if (!preg_match(static::getRegularExpression(), $input)) {
            throw new InvalidStringForValueObjectException(
                $input,
                new \ReflectionClass(self::class)
            );
        }
        if (ApieLib::hasAlias(EntityInterface::class)) {
            if (null === self::split($input)) {
                throw new InvalidStringForValueObjectException($input, new \ReflectionClass(self::class));
            }
        }
    }

    /**
     * @return array{class-string<EntityInterface>, IdentifierInterface<EntityInterface>}|null
     */
    private static function split(string $input): ?array
    {
        $alias = ReflectionTypeFactory::createReflectionType(ApieLib::getAlias(EntityInterface::class));
        $split = explode('/', $input, 2);
        assert(count($split) === 2);
        if ($alias instanceof \ReflectionNamedType) {
            $class = ConverterUtils::toReflectionClass($alias->getName());
            if ($class && $class->getShortName() === $split[0]) {
                $identifier = ConverterUtils::toReflectionClass($class->getMethod('getId')->getReturnType());
                return [$class->name, $identifier->getMethod('fromNative')->invoke(null, $split[1])];
            }
            return null;
        }
        assert($alias instanceof \ReflectionUnionType);
        foreach ($alias->getTypes() as $type) {
            $class = ConverterUtils::toReflectionClass($type);
            if ($class && $class->getShortName() === $split[0]) {
                $identifier = ConverterUtils::toReflectionClass($class->getMethod('getId')->getReturnType());
                return [$class->name, $identifier->getMethod('fromNative')->invoke(null, $split[1])];
            }
        }
        return null;
    }

    public static function getRegularExpression(): string
    {
        // if (!ApieLib::hasAlias(EntityInterface::class)) {
        return '/^[A-Z][a-zA-Z0-9]*\/[a-zA-Z0-9_-]+$/';
        /*}
        $regex = [];
        $alias = ReflectionTypeFactory::createReflectionType(ApieLib::getAlias(EntityInterface::class));
        $types = $alias instanceof \ReflectionNamedType ? [$alias] : $alias->getTypes();
        foreach ($types as $type) {
            $class = ConverterUtils::toReflectionClass($type);
            if ($class) {
                $identifier = ConverterUtils::toReflectionClass($class->getMethod('getId')->getReturnType());
                if (in_array(HasRegexValueObjectInterface::class, $identifier->getInterfaceNames())) {
                    $regex[] = $class->getShortName()
                        . '/'
                        . RegexUtils::removeDelimiters(
                            $identifier->getMethod('getRegularExpression')->invoke(null)
                        );
                } else {
                    $regex[] = $class->getShortName() . '/.+';
                }
            }
        }
        return '#^(' . implode(')|(', $regex) . ')$#';*/
    }

    public static function createRandom(Generator $faker): self
    {
        $alias = ReflectionTypeFactory::createReflectionType(ApieLib::getAlias(EntityInterface::class));
        // @phpstan-ignore method.notFound
        $types = $alias instanceof \ReflectionNamedType ? [$alias] : $alias->getTypes();
        $entityClass = ConverterUtils::toReflectionClass($faker->randomElement($types));
        $identifierClass = IdentifierUtils::entityClassToIdentifier($entityClass);
        $identifier = $faker->fakeClass($identifierClass->name);
        return new self($entityClass->getShortName() . '/' . $identifier->toNative());
    }
}
