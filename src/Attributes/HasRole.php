<?php
namespace Apie\Core\Attributes;

use Apie\Common\ContextConstants;
use Apie\Common\Interfaces\HasRolesInterface;
use Apie\Core\Context\ApieContext;

/**
 * Tell Apie you need to be logged in with a specific role to see/execute a class/method/property.
 */
final class HasRole implements ApieContextAttribute
{
    /** @var array<int, string> */
    public readonly array $roles;
    public function __construct(
        string... $roles
    ) {
        $this->roles = $roles;
    }
    public function applies(ApieContext $context): bool
    {
        $user = $context->getContext(ContextConstants::AUTHENTICATED_USER, false);
        if ($user instanceof HasRolesInterface) {
            $roles = $user->getRoles()->toArray();
            $diff = array_intersect($this->roles, $roles);
            return !empty($diff);
        }

        return false;
    }
}