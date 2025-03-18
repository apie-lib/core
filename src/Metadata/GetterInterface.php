<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;

interface GetterInterface
{
    public function getValue(object $object, ApieContext $apieContext): mixed;
}
