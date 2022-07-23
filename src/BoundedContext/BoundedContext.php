<?php
namespace Apie\Core\BoundedContext;

use Apie\Core\Lists\ReflectionClassList;
use Apie\Core\Lists\ReflectionMethodList;

final class BoundedContext
{
    public function __construct(
        public readonly ReflectionClassList $resources,
        public readonly ReflectionMethodList $actions,
    ) {
    }
}
