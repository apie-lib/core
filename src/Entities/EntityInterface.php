<?php
namespace Apie\Core\Entities;

use Apie\Core\Identifiers\IdentifierInterface;

interface EntityInterface
{
    public function getId(): IdentifierInterface;
}
