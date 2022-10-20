<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * Used to indicate a column priority so the order of the columns can be indicated.
 *
 * @TODO: use column priority in cms overview
 */
#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
final class ColumnPriority
{
    public int $priority;

    public function __construct(int $priority)
    {
        $this->priority = $priority;
    }
}
