<?php
namespace Apie\Core\Exceptions;

final class ObjectIsEmpty extends ApieException
{
    private function __construct(string $objectName)
    {
        parent::__construct($objectName . ' is empty!');
    }

    public static function createForList(): self
    {
        return new self('ItemList');
    }

    public static function createForHashmap(): self
    {
        return new self('ItemHashmap');
    }
}
