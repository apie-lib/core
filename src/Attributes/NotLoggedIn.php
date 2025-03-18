<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Apie\Core\ContextConstants;

/**
 * Tell Apie you should not be logged in to see/execute a class/method/property.
 */
final class NotLoggedIn implements ApieContextAttribute
{
    public function applies(ApieContext $context): bool
    {
        return !$context->hasContext(ContextConstants::AUTHENTICATED_USER);
    }
}
