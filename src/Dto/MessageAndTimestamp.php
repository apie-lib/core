<?php
namespace Apie\Core\Dto;

use DateTimeImmutable;

class MessageAndTimestamp implements DtoInterface
{
    public function __construct(
        public string $message,
        public DateTimeImmutable $timestamp
    ) {
    }
}
