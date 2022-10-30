<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use ReflectionType;

interface FieldInterface
{
    public function isRequired(): bool;

    public function isField(): bool;

    public function appliesToContext(ApieContext $apieContext): bool;

    public function getFieldPriority(): ?int;

    public function getTypehint(): ?ReflectionType;
}
