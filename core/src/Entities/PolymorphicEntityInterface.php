<?php
namespace Apie\Core\Entities;

use Apie\Core\Other\DiscriminatorMapping;

/**
 * All Apie entities that use inheritance should implement this interface.
 */
interface PolymorphicEntityInterface extends EntityInterface
{
    public static function getDiscriminatorMapping(): DiscriminatorMapping;
}
