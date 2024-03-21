<?php
namespace Apie\Core\Attributes;

use Apie\Common\ContextConstants;
use Apie\Core\Context\ApieContext;
use ReflectionClass;

/**
 * Tell Apie you need to be logged in to see/execute a class/method/property.
 */
final class LoggedIn implements ApieContextAttribute
{
    /**
     * @param class-string<object>|null $className
     */
    public function __construct(
        public ?string $className = null
    ) {
    }
    public function applies(ApieContext $context): bool
    {
        if (!$context->hasContext(ContextConstants::AUTHENTICATED_USER)) {
            return false;
        }
        if ($this->className === null) {
            return true;
        }
        $authenticatedUser = $context->getContext(ContextConstants::AUTHENTICATED_USER);
        $refl = new ReflectionClass($this->className);
        return $refl->isInstance($authenticatedUser);
    }
}