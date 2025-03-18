<?php
namespace Apie\Core\Metadata\Concerns;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ValueOptionList;

trait NoValueOptions
{
    public function getValueOptions(ApieContext $context, bool $runtimeFilter = false): ?ValueOptionList
    {
        return null;
    }
}
