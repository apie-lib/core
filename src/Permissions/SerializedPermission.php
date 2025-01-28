<?php

namespace Apie\Core\Permissions;

use Apie\Core\Lists\PermissionList;
use Apie\Core\ValueObjects\Interfaces\HasRegexValueObjectInterface;
use Apie\Core\ValueObjects\Interfaces\ValueObjectInterface;
use Apie\Core\ValueObjects\IsStringWithRegexValueObject;

final class SerializedPermission implements PermissionInterface, HasRegexValueObjectInterface
{
    use IsStringWithRegexValueObject;

    public function getPermissionIdentifiers(): PermissionList
    {
        return new PermissionList([$this->internal]);
    }

    public static function getRegularExpression(): string
    {
        return '/^[a-z:]+$/';
    }
}