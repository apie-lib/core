<?php
namespace Apie\Core\Entities;

use DateTimeInterface;

interface RequiresRecalculatingInterface extends EntityInterface
{
    public function getDateToRecalculate(): ?DateTimeInterface;
}
