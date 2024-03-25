<?php
namespace Apie\Core\Permissions;

use Apie\Core\Lists\PermissionList;
use Stringable;

interface PermissionInterface
{
    public function getPermissionIdentifiers(): PermissionList;
}