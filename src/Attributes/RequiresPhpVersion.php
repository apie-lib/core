<?php
namespace Apie\Core\Attributes;

use Apie\Core\Context\ApieContext;
use Composer\Semver\Semver;

/**
 * Tell Apie you can only pick this option if the server is a specific php version.
 */
final class RequiresPhpVersion implements ApieContextAttribute
{
    public function __construct(
        public string $constraints
    ) {
    }
    public function applies(ApieContext $context): bool
    {
        return Semver::satisfies(
            PHP_VERSION,
            $this->constraints
        );
    }
}
