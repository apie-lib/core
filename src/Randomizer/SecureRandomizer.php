<?php
namespace Apie\Core\Randomizer;

class SecureRandomizer implements RandomizerInterface
{
    public function randomElements(array $elements, int $count): array
    {
        $res = [];
        for ($i = 0; $i < $count; $i++) {
            $res[] = $this->randomElement($elements);
        }
        return $res;
    }
    public function randomElement(array $elements): mixed
    {
        return $elements[random_int(0, count($elements) - 1)];
    }
    public function randomDigit(): int
    {
        return random_int(0, 9);
    }
    public function numberBetween(int $minLength, int $maxLength): int
    {
        return random_int($minLength, $maxLength);
    }

    public function shuffle(array& $list): void
    {
        $count = count($list);
        // n log n is sufficient number of shuffles between 2 items
        $numberOfShuffles = ($count * log10($count));
        for ($i = 0; $i < $numberOfShuffles; $i++) {
            $left = random_int(0, $count - 1);
            $right = random_int(0, $count - 1);
            $tmp = $list[$left];
            $list[$left] = $list[$right];
            $list[$right] = $tmp;
        }
    }
}
