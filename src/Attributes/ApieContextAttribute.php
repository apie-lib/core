<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;

/**
 * Interface used by all Apie attribtues related to ApieContext.
 */
interface ApieContextAttribute
{
    public function applies(ApieContext $context): bool;
}
