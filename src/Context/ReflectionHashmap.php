<?php
namespace Apie\Core\Context;

use Apie\Core\Attributes\ColumnPriority;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Other\DiscriminatorMapping;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionType;

/**
 * Contains a list of methods and/or properties.
 */
final class ReflectionHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): ReflectionType|ReflectionMethod|ReflectionProperty|ReflectionParameter|DiscriminatorMapping
    {
        return parent::offsetGet($offset);
    }

    public function sort(): self
    {
        $arrayCopy = array_map(
            function (ReflectionType|ReflectionMethod|ReflectionProperty|ReflectionParameter|DiscriminatorMapping $value, string $key) {
                $prioFromType = $this->getPrioFromType($value);
                return [$value, $prioFromType ?? $this->getPrioFromKey($key), $key];
            },
            $this->internalArray,
            array_keys($this->internalArray)
        );
        usort($arrayCopy, function (array $input1, array $input2) {
            return $input1[1] <=> $input2[1];
        });
        $newArray = [];
        foreach ($arrayCopy as $input) {
            $newArray[$input[2]] = $input[0];
        }
        return new self($newArray);
    }

    private function getPrioFromKey(string $input): int
    {
        if (stripos($input, 'status') !== false) {
            return -150;
        }
        $ratings = [
            'id' => -300,
            'name' => -250,
            'email' => -200,
            'description' => 100,
        ];
        return $ratings[$input] ?? 0;
    }

    private function getPrioFromType(ReflectionType|ReflectionMethod|ReflectionProperty|ReflectionParameter|DiscriminatorMapping $value): ?int
    {
        if ($value instanceof DiscriminatorMapping) {
            return -280;
        }
        
        $attributes = $value->getAttributes(ColumnPriority::class);
        if (empty($attributes)) {
            return null;
        }

        $attribute = reset($attributes);
        return $attribute->newInstance()->priority;
    }
}
