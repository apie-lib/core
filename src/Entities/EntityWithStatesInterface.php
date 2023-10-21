<?php
namespace Apie\Core\Entities;

use Apie\Core\Lists\StringList;

interface EntityWithStatesInterface extends EntityInterface
{
    public function provideAllowedMethods(): StringList;
}
