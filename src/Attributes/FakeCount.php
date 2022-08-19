<?php
namespace Apie\Core\Attributes;

use Attribute;

/**
 * This is only used by the FakeDataLayer to tell the amount of faked data the list should simulate.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class FakeCount
{
    public int $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }
}
