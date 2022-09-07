<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\Context\ApieContext;

interface ContextBuilderInterface
{
    public function process(ApieContext $context): ApieContext;
}
