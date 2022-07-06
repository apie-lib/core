<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;

interface ApieContextAttribute
{
    public function applies(ApieContext $context): bool;
}
