<?php
namespace Apie\Core\Policies;

use Apie\Core\Context\ApieContext;

interface PolicyProviderInterface
{
    public function getPolicyFor(ApieContext $apieContext, string $action): object;
}
