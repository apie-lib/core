<?php
namespace Apie\Core\ContextBuilders;

use Apie\Core\Context\ApieContext;

interface ContextBuilderInterface
{
    /**
     * context key for raw contents for example the request body being JSON decoded or all the input arguments of a console command.
     */
    public const RAW_CONTENTS = 'raw-contents';

    /**
     * context key for indicating the resource that is being edited/created.
     */
    public const RESOURCE = 'resource';

    public function process(ApieContext $context): ApieContext;
}
