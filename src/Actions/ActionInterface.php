<?php
namespace Apie\Core\Actions;

use Apie\Core\Context\ApieContext;

interface ActionInterface
{
    public function __invoke(ApieContext $context, array $rawContents): mixed;
}
