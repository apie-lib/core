<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Menu header translations. Keys reflect the tree structure of the menu')]
#[ExampleValue('apie.menu.header.root.test.authenticated')]
class MenuHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = '(menu.header|(menu(\.[^.]+(\.[^.]+)*))*\.header)';
}
