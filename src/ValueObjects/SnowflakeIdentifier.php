<?php
namespace Apie\Core\ValueObjects;

use Apie\Core\Exceptions\InvalidTypeException;
use Apie\Core\RegexUtils;
use Apie\Core\Utils\ConverterUtils;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\RegexTools\CompiledRegularExpression;
use ReflectionClass;
use ReflectionNamedType;

abstract class SnowflakeIdentifier implements ValueObjectInterface, HasRegexValueObjectInterface
{
    private string $calculated;

    abstract protected static function getSeparator(): string;

    final public function toNative(): string
    {
        if (!isset($this->calculated)) {
            $prefix = '';
            if (is_callable([static::class, 'getPrefix'])) {
                $prefix = static::getPrefix() . '-';
            }
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

            $this->calculated = $prefix . implode($separator, $result);
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
        $prefix = '';
        if (is_callable([static::class, 'getPrefix'])) {
            $prefix = static::getPrefix() . '-';
        }
        if (strpos($input, $prefix) === 0) {
            $input = substr($input, strlen($prefix));
        } else {
            throw new InvalidStringForValueObjectException($input, new ReflectionClass(static::class));
        }
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

    final public static function getRegularExpression(): string
    {
        $refl = new ReflectionClass(static::class);
        $parameters = $refl->getConstructor()->getParameters();
        $separator = preg_quote(static::getSeparator());

        $expressions = [];
        $prefix = '';
        if (is_callable([static::class, 'getPrefix'])) {
            $prefix = static::getPrefix() . '-';
        }
        if ($prefix !== '') {
            $expressions[] = preg_quote($prefix, static::getSeparator());
        }
        foreach ($parameters as $parameter) {
            $parameterType = $parameter->getType();
            if (!($parameterType instanceof ReflectionNamedType)) {
                throw new InvalidTypeException($parameterType, 'ReflectionNamedType');
            }
            $regex = '[^' . $separator . ']+';
            $class = ConverterUtils::toReflectionClass($parameterType);
            if (in_array(HasRegexValueObjectInterface::class, $class?->getInterfaceNames() ?? [])) {
                $foundRegex = '(' . RegexUtils::removeDelimiters($class->getMethod('getRegularExpression')->invoke(null)) . ')';
                if (strpos($foundRegex, '?=') === false) {
                    $regex = $foundRegex;
                }
            } else {
                switch ($parameterType->getName()) {
                    case 'int':
                        $regex = '-?(0|[1-9]\d*)';
                        break;
                    case 'float':
                        $regex = '-?(0|[1-9]\d*)(\.\d+)?';
                        break;
                }
            }
            $expressions[] = $regex;
            $expressions[] = $separator;
        }
        array_pop($expressions);

        $expressions = array_map(
            function (string $expression) {
                return CompiledRegularExpression::createFromRegexWithoutDelimiters($expression)
                            ->removeStartAndEndMarkers();
            },
            $expressions
        );
        array_unshift($expressions, CompiledRegularExpression::createFromRegexWithoutDelimiters('^'));
        array_push($expressions, CompiledRegularExpression::createFromRegexWithoutDelimiters('$'));

        $tmp = CompiledRegularExpression::createFromRegexWithoutDelimiters('');

        return $tmp->merge(...$expressions)->__toString();
    }
}
