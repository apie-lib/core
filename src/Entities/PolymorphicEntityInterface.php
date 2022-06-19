<?php
namespace Apie\Core\Entities;

use Apie\Core\Other\DiscriminatorMapping;

interface PolymorphicEntityInterface {
    public function getDiscriminatorMapping(): DiscriminatorMapping;
}