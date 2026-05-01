<?php
namespace Apie\Core\Policies;

class FallbackPolicy
{
    public function __construct(
        private readonly bool $allow
    ) {
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): bool
    {
        return $this->allow;
    }
}
