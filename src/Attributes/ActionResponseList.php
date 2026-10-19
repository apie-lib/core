<?php
namespace Apie\Core\Attributes;

use Apie\Core\Actions\ActionResponseStatus;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class ActionResponseList
{
    /**
     * @var array<int, ActionResponseStatus> $statuses
     */
    public readonly array $statuses;

    public function __construct(ActionResponseStatus... $statuses)
    {
        $this->statuses = $statuses;
    }
}
