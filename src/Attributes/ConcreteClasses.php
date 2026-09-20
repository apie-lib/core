<?php
namespace Apie\Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class ConcreteClasses
{
    /** @var array<string> $classes */
    public readonly array $classes;
    public function __construct(string... $classes)
    {
        if (empty($classes)) {
            throw new \LogicException('There should be at least one defined class');
        }
        $this->classes = $classes;
    }
}
