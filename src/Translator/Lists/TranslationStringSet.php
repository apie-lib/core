<?php
namespace Apie\Core\Translator\Lists;

use Apie\Core\Lists\ItemSet;
use Apie\Core\Translator\ValueObjects\AbstractTranslation;

/**
 * @extends ItemSet<AbstractTranslation>
 */
final class TranslationStringSet extends ItemSet
{
    protected bool $mutable = false;

    public function offsetGet(mixed $offset): AbstractTranslation
    {
        return parent::offsetGet($offset);
    }

    public function toArray(): array
    {
        $list = parent::toArray();

        usort(
            $list,
            function (AbstractTranslation $a, AbstractTranslation $b) {
                return $a->getSpecifity() <=> $b->getSpecifity();
            }
        );

        return $list;
    }

    /**
     * @return array<int, AbstractTranslation>
     */
    public function toArrayWithSimplifications(): array
    {
        $done = [];
        $result = [];
        $todo = $this->toArray();

        while (!empty($todo)) {
            $item = array_pop($todo);
            $key = $item->toNative();
            if (!empty($done[$key])) {
                continue;
            }
            $done[$key] = true;
            $result[] = $item;
            $todo = [...$todo, ...$item->getSimplifications()];
        }

        return $result;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toNestedArray(): array
    {
        /** @var array<array-key, mixed> $result */
        $result = [];

        foreach ($this->toArrayWithSimplifications() as $translationString) {
            /** @var AbstractTranslation $translationString */
            $key = $translationString->toNative();
            $value = $translationString->getFallbackText();
            $parts = explode('.', $key);
            $current = &$result;

            foreach ($parts as $index => $part) {
                $isLast = $index === array_key_last($parts);

                if ($isLast) {
                    if (!isset($current[$part])) {
                        $current[$part] = $value;
                    } elseif (is_array($current[$part])) {
                        // Node already became an array earlier.
                        $current[$part][''] = $value;
                    } else {
                        // Duplicate key; overwrite or throw, depending on your needs.
                        $current[$part] = $value;
                    }
                } else {
                    if (!isset($current[$part])) {
                        $current[$part] = [];
                    } elseif (!is_array($current[$part])) {
                        // Convert existing scalar into an array.
                        $current[$part] = [
                            '' => $current[$part],
                        ];
                    }

                    $current = &$current[$part];
                }
            }

            unset($current);
        }

        return $result;
    }
}
