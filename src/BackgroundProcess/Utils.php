<?php
namespace Apie\Core\BackgroundProcess;

use Apie\Core\BoundedContext\BoundedContextId;
use Apie\Core\ContextBuilders\ContextBuilderFactory;
use Apie\Core\ContextConstants;
use Apie\Core\Datalayers\ApieDatalayer;

final class Utils
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    public static function runBackgroundProcess(
        SequentialBackgroundProcessIdentifier $id,
        ?BoundedContextId $boundedContextId,
        ApieDatalayer $apieDatalayer,
        ContextBuilderFactory $contextBuilderFactory
    ): void {
        $process = $apieDatalayer->find($id, $boundedContextId);
        if ($process->getStatus() !== BackgroundProcessStatus::Active) {
            return;
        }
        $context = [];
        if ($boundedContextId) {
            $context[ContextConstants::BOUNDED_CONTEXT_ID] = $boundedContextId->toNative();
            $context[BoundedContextId::class] = $boundedContextId;
        }
            
        $apieContext = $contextBuilderFactory->createGeneralContext($context);
        $process->runStep($apieContext);
        $apieDatalayer->persistExisting($process, $boundedContextId);
    }
}
