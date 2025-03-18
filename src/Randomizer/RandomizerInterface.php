<?php
namespace Apie\Core\Randomizer;

interface RandomizerInterface
{
    /**
     * @param array<int, mixed> $elements
     * @return array<int, mixed>
     */
    public function randomElements(array $elements, int $count): array;
    /**
     * @param array<int, mixed> $elements
     */
    public function randomElement(array $elements): mixed;
    public function randomDigit(): int;
    public function numberBetween(int $minLength, int $maxLength): int;
    /**
     * @param array<int|string, mixed>& $list
     */
    public function shuffle(array& $list): void;
}
