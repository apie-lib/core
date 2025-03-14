<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use ReflectionClass;
use ReflectionNamedType;

abstract class SnowflakeIdentifier implements ValueObjectInterface
{
    private string $calculated;

    abstract protected static function getSeparator(): string;

    final public function toNative(): string
    {
        if (!isset($this->calculated)) {
            $refl = new ReflectionClass($this);
            $separator = static::getSeparator();
            $result = [];
            foreach ($refl->getConstructor()->getParameters() as $parameter) {
                $propertyName = $parameter->getName();
                $propertyValue = $refl->getProperty($propertyName)->getValue($this);
                $stringPropertyValue = Utils::toString($propertyValue);
                if (strpos($stringPropertyValue, $separator) !== false) {
                    throw new InvalidStringForValueObjectException($stringPropertyValue, $propertyValue);
                }
                $result[] = $stringPropertyValue;
            }

            $this->calculated = implode($separator, $result);
        }
        return $this->calculated;
    }

    final public function __toString(): string
    {
        return $this->toNative();
    }

    final public function jsonSerialize(): string
    {
        return $this->toNative();
    }

    public static function fromNative(mixed $input): self
    {
        $input = Utils::toString($input);
        $refl = new ReflectionClass(static::class);
        $parameters = $refl->getConstructor()->getParameters();
        $separator = static::getSeparator();
        $split = explode($separator, $input, count($parameters));
        if (count($split) !== count($parameters)) {
            throw new InvalidStringForValueObjectException($input, new ReflectionClass(static::class));
        }
        $constructorArguments = [];
        foreach ($parameters as $key => $parameter) {
            $parameterType = $parameter->getType();
            if (!($parameterType instanceof ReflectionNamedType)) {
                throw new InvalidTypeException($parameterType, 'ReflectionNamedType');
            }
            if ($parameterType->allowsNull() && $split[$key] === '') {
                $constructorArguments[] = null;
            } else {
                $constructorArguments[] = Utils::toTypehint($parameterType, $split[$key]);
            }
        }
        return $refl->newInstanceArgs($constructorArguments);
    }
}