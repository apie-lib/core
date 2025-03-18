<?php
namespace Apie\Core\Attributes;

use Apie\Core\Dto\CmsInputOption;
use Apie\Core\Lists\StringList;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD|Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
final class CmsSingleInput
{
    public readonly StringList $types;

    /**
     * @param array<int, string> $types
     */
    public function __construct(array $types, public readonly CmsInputOption $options = new CmsInputOption())
    {
        $this->types = new StringList($types);
    }
}
