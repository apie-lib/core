<?php
namespace Apie\Core\Context;

use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Metadata\Fields\FieldInterface;
use Apie\Core\Metadata\GetterInterface;
use Apie\Core\Metadata\SetterInterface;

/**
 * Contains a list of methods and/or properties.
 */
final class MetadataFieldHashmap extends ItemHashmap
{
    public function offsetGet(mixed $offset): FieldInterface
    {
        return parent::offsetGet($offset);
    }

    public function filterOnContext(ApieContext $apieContext, ?bool $getters = null, ?bool $setters = null): self
    {
        $list = array_filter(
            $this->internalArray,
            function (FieldInterface $field) use ($apieContext, $getters, $setters) {
                if ($getters !== null && ($getters xor ($field instanceof GetterInterface))) {
                    return false;
                }
                if ($setters !== null && ($setters xor ($field instanceof SetterInterface))) {
                    return false;
                }
                return $field->appliesToContext($apieContext);
            }
        );

        return new self($list);
    }

    public function sort(): self
    {
        $arrayCopy = array_map(
            function (FieldInterface $value, string $key) {
                $prioFromType = $value->getFieldPriority();
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
}
