<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;

#[Description('Menu header translations. Keys reflect the tree structure of the menu')]
#[ExampleValue('apie.menu.header.root.test.authenticated')]
class MenuHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = '(menu.header|(menu(\.[^.]+(\.[^.]+)*))*\.header)';

    public function getFallbackText(): string
    {
        if (preg_match('/\.([^.]+)\.header$/', $this->middleSection, $matches)) {
            return $matches[1] ? ucfirst($matches[1]) : 'Home';
        }
        return 'Home';
    }
}
