<?php
namespace Apie\Core\Lists;

interface Arrayable
{
    /**
     * @return array<string|int|null, mixed>
     */
    public function toArray(): array;
}
