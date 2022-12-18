<?php
namespace Apie\Core\Metadata\Fields;

use Apie\Core\Context\ApieContext;
use ReflectionType;

interface FallbackFieldInterface
{
    public function getMissingValue(): mixed;
}