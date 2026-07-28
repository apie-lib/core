<?php
namespace Apie\Core\Translator\ValueObjects;

use Apie\Core\Attributes\Description;
use Apie\Core\Attributes\ExampleValue;
use Apie\Core\Context\ApieContext;
use Apie\Core\Identifiers\SnakeCaseSlug;
use Apie\Core\Lists\ItemHashmap;

#[Description('Menu header translations. Keys reflect the tree structure of the menu')]
#[ExampleValue('apie.menu.header.root.test.authenticated')]
final class MenuHeader extends AbstractTranslation
{
    protected const MIDDLE_REGEX = '(menu.header|(menu(\.[^.]+(\.[^.]+)*))*\.header)';

    public function getFallbackText(): string
    {
        if (preg_match('/\.([^.]+)\.header$/', $this->middleSection, $matches)) {
            return $matches[1] ? ucfirst(SnakeCaseSlug::fromNative($matches[1])->humanize()) : 'Home';
        }
        return 'Home';
    }

    public static function createRoot(ApieContext $context, string $path = ''): MenuHeader
    {
        $prefix = TranslationStringPrefix::fromApieContext($context);
        $suffix = TranslationStringSuffix::fromApieContext($context);
        $pathList = array_filter(
            array_map(
                function (string $pathItem) {
                    return $pathItem ? SnakeCaseSlug::fromText($pathItem)->toNative() : '';
                },
                explode('.', $path)
            )
        );
        
        return new static(
            $prefix,
            $path ? ('menu.' . implode('.', $pathList) . '.header') : 'menu.header',
            $suffix,
            new ItemHashmap()
        );
    }
}
