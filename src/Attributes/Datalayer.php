<?php
namespace Apie\Core\Attributes;

use Attribute;
use Apie\Core\Datalayers\ApieDatalayer;

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