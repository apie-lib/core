<?php
namespace Apie\Core\Permissions;

use Apie\Core\Lists\PermissionList;

interface RequiresPermissionsInterface
{
    public function getRequiredPermissions(): PermissionList;
}
