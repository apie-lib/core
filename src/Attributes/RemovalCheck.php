<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Attribute;

/**
 * By default resources can not be removed. This attribute needs to be defined before it can be removed.
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS)]
final class RemovalCheck
{
    private StaticCheck|RuntimeCheck $check;

    public function __construct(StaticCheck|RuntimeCheck $check)
    {
        $this->check = $check;
    }

    public function isStaticCheck(): bool
    {
        return $this->check instanceof StaticCheck;
    }

    public function isRuntimeCheck(): bool
    {
        return $this->check instanceof RuntimeCheck;
    }
    
    public function applies(ApieContext $context): bool
    {
        return $this->check->applies($context);
    }
}
