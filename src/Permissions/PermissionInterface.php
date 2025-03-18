<?php
namespace Apie\Core\Permissions;

use Apie\Core\Lists\PermissionList;

interface PermissionInterface
{
    public function getPermissionIdentifiers(): PermissionList;
}
