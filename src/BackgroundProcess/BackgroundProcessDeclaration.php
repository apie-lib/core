<?php
namespace Apie\Core\BackgroundProcess;

use Apie\Core\Context\ApieContext;
use Apie\Core\Lists\ItemHashmap;
use Apie\Core\Lists\ItemList;

interface BackgroundProcessDeclaration
{
    /**
     * @return array<int|string, callable(ApieContext, ItemHashmap|itemList): mixed>
     */
    public static function retrieveDeclaration(int $version): array;

    public function getCurrentVersion(): int;

    public static function getMaxRetries(int $version): int;
}
