<?php


namespace Apie\Core\Exceptions;

interface FieldNameAwareInterface
{
    public function getFieldName(): string;
}
