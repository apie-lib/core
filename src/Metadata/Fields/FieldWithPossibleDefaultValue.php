<?php
namespace Apie\Core\Metadata\Fields;

interface FieldWithPossibleDefaultValue extends FieldInterface
{
    public function hasDefaultValue(): bool;

    public function getDefaultValue(): mixed;
}