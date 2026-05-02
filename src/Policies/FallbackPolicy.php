<?php
namespace Apie\Core\Policies;

class FallbackPolicy
{
    /**
     * @param array<string, mixed> $arguments
     */
    public function __call(string $name, array $arguments): bool|null
    {
        return null;
    }
}
