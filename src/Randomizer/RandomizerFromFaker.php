<?php
namespace Apie\Core\Randomizer;

use Faker\Generator;

class RandomizerFromFaker implements RandomizerInterface
{
    public function __construct(private readonly Generator $generator)
    {
    }

    public function randomElements(array $elements, int $count): array
    {
        return $this->generator->randomElements($elements, $count);
    }
    public function randomElement(array $elements): mixed
    {
        return $this->generator->randomElement($elements);
    }
    public function randomDigit(): int
    {
        return $this->generator->randomDigit();
    }
    public function numberBetween(int $minLength, int $maxLength): int
    {
        return $this->generator->numberBetween($minLength, $maxLength);
    }

    public function shuffle(array& $list): void
    {
        shuffle($list);
    }
}
