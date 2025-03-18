<?php
namespace Apie\Core\Metadata;

use Apie\Core\Context\ApieContext;

interface SetterInterface
{
    public function setValue(object $object, mixed $value, ApieContext $apieContext): void;
    public function markValueAsMissing(): void;
}
