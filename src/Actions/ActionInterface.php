<?php
namespace Apie\Core\Actions;

use Apie\Core\Context\ApieContext;

interface ActionInterface
{
    /**
     * @param array<string|int, mixed> $rawContents
     */
    public function __invoke(ApieContext $context, array $rawContents): mixed;
}
