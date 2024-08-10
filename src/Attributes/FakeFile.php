<?php
namespace Apie\Core\Attributes;

use Apie\Faker\FileFakerFactory;
use Apie\Faker\Interfaces\ApieFileFaker;
use Attribute;

/**
 * Adding a FakeFile attribute allows you to specify that this is an uploaded file
 * and that is should use file fakers of a specific class.
 * @see FileFakerFactory
 */
#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_CLASS)]
final class FakeFile
{
    /**
     * @param class-string<ApieFileFaker> $className
     */
    public function __construct(public $className)
    {
    }
}
