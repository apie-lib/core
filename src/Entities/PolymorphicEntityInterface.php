<?php
namespace Apie\Core\Entities;

use Apie\Core\Other\DiscriminatorMapping;

interface PolymorphicEntityInterface extends EntityInterface
{
    public static function getDiscriminatorMapping(): DiscriminatorMapping;
}
