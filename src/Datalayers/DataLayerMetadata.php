<?php
namespace Apie\Core\Datalayers;

use DateTimeImmutable;

final class DataLayerMetadata
{
    public private(set) string $etag;

    public function __construct(
        public readonly DateTimeImmutable $createdAt,
        public readonly DateTimeImmutable $updatedAt,
        public readonly string $createdInApieVersion,
        public readonly string $updatedInApieVersion,
    ) {
        $this->etag = hash(
            'sha256',
            $this->updatedInApieVersion . ':' . $this->updatedAt->format('U.u')
        );
    }

    public function withUpdatedMetadata(DateTimeImmutable $updatedAt, string $apieVersion): self
    {
        return new self(
            $this->createdAt,
            $updatedAt,
            $this->createdInApieVersion,
            $apieVersion
        );
    }
}
