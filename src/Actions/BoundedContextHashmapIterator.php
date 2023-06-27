<?php
namespace Apie\Core\Actions;

use Apie\Core\BoundedContext\BoundedContext;
use Apie\Core\BoundedContext\BoundedContextHashmap;
use Apie\Core\Entities\EntityInterface;
use Iterator;
use ReflectionClass;

/**
 * @implements Iterator<int, BoundedContextEntityTuple>
 */
class BoundedContextHashmapIterator implements Iterator
{
    private int $counter = 0;
    private ?BoundedContext $currentBoundedContext = null;
    private ?ReflectionClass $currentValue = null;
    /**
     * @var array<int, BoundedContext>
     */
    private array $boundedContextTodo = [];

    /**
     * @var array<int, EntityInterface>
     */
    private array $entityTodo = [];

    public function __construct(private readonly BoundedContextHashmap $boundedContextHashmap)
    {
    }
    public function current(): ?BoundedContextEntityTuple
    {
        return new BoundedContextEntityTuple(
            $this->currentBoundedContext,
            $this->currentValue
        );
    }
    public function key(): int
    {
        return $this->counter;
    }
    public function next(): void
    {
        $this->currentValue = null;
        while (empty($this->entityTodo)) {
            $boundedContext = array_shift($this->boundedContextTodo);
            if (!$boundedContext) {
                return;
            }
            $this->currentBoundedContext = $boundedContext;
            $this->entityTodo = $this->currentBoundedContext->resources->toArray();
        }
        $this->counter++;
        $this->currentValue = array_shift($this->entityTodo);
    }
    public function rewind(): void
    {
        $this->counter = 0;
        $this->boundedContextTodo = $this->boundedContextHashmap->toArray();
        $this->entityTodo = [];
        $this->next();
    }
    public function valid(): bool
    {
        return null !== $this->currentValue;
    }
}
