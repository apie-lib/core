<?php
namespace Apie\Core\Other;

final class StreamBucketObject
{
    public function __construct(
        public string $data,
        public int $dataLength
    ) {
    }
}
