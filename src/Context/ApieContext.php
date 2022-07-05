<?php
namespace Apie\Core\Context;

use Apie\Core\Exceptions\IndexNotFoundException;

final class ApieContext {
    private array $context = [];

    public function withContext(string $key, mixed $value): self
    {
        $instance = clone $this;
        $instance->context[$key] = $value;
        return $instance;
    }

    public function hasContext(string $key): bool
    {
        return array_key_exists($key, $this->context);
    }

    public function getContext(string $key): mixed
    {
        if (!array_key_exists($key, $this->context)) {
            throw new IndexNotFoundException($key);
        }

        return $this->context[$key];
    }
}