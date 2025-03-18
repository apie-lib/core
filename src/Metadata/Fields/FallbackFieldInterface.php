<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;

interface FallbackFieldInterface
{
    public function getMissingValue(ApieContext $apieContext): mixed;
}
