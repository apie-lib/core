<?php
namespace Apie\Core\Attributes;

use Apie\Core\Datalayers\ApieDatalayer;
use Attribute;

/**
 * Attribute to specify which datalayer to use for this entity.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Datalayer
{
    /**
     * @param class-string<ApieDatalayer> $datalayerClass
     */
    public function __construct(
        public readonly string $datalayerClass
    ) {
    }
}
