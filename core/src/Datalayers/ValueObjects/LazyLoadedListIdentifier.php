<?php
namespace Apie\Core\Datalayers\ValueObjects;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\Datalayers\Lists\LazyLoadedList;
use Apie\Core\Entities\EntityInterface;
use Apie\Core\Identifiers\IdentifierInterface;
use Apie\Core\ValueObjects\Exceptions\InvalidStringForValueObjectException;
use Apie\Core\ValueObjects\Utils;
use ReflectionClass;
use Throwable;

/**
 * @template T of EntityInterface
 * @implements IdentifierInterface<LazyLoadedList<T>>
 */
final class LazyLoadedListIdentifier implements IdentifierInterface
{
    private BoundedContextId $boundedContextId;

    /**
     * @var ReflectionClass<T>
     */
    private ReflectionClass $class;

    public function __construct(string $input)
    {
        $split = explode(',', $input);
        if (count($split) !== 2) {
            throw new InvalidStringForValueObjectException($input, $this);
        }
        try {
            $this->boundedContextId = new BoundedContextId($split[0]);
            $this->class = new ReflectionClass($split[1]);
        } catch (Throwable $throwable) {
            throw new InvalidStringForValueObjectException($input, $this, $throwable);
        }
    }

    public function getBoundedContextId(): BoundedContextId
    {
        return $this->boundedContextId;
    }

    /**
     * @return ReflectionClass<T>
     */
    public function getClass(): ReflectionClass
    {
        return $this->class;
    }

    /**
     * @template U of EntityInterface
     * @param ReflectionClass<U> $class
     * @return LazyLoadedListIdentifier<U>
     */
    public static function createFrom(BoundedContextId $boundedContextId, ReflectionClass $class): self
    {
        return new self($boundedContextId . ',' . $class->name);
    }

    /**
     * @return LazyLoadedListIdentifier<EntityInterface>
     */
    public static function fromNative(mixed $input): self
    {
        return new self(Utils::toString($input));
    }

    public function toNative(): string
    {
        return $this->boundedContextId . ',' . $this->class->name;
    }

    public function asUrl(): string
    {
        return '/' . $this->boundedContextId . '/' . $this->class->getShortName();
    }

    /**
     * @return ReflectionClass<LazyLoadedList<T>>
     */
    public static function getReferenceFor(): ReflectionClass
    {
        return new ReflectionClass(LazyLoadedList::class);
    }
}
