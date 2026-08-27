<?php
namespace Apie\Core\Utils;

use ICanBoogie\InflectionsNotFound;
use ICanBoogie\Inflector as ICanBoogieInflector;

final class Inflector 
{
    public static function get(string $locale): ICanBoogieInflector
    {
        try {
        if (preg_match('/^(?<language>[^._-]+)/', $locale, $matches)) {
            return ICanBoogieInflector::get($matches['language']);
        }
        return ICanBoogieInflector::get($locale);
        } catch (InflectionsNotFound) {
            return ICanBoogieInflector::get('en');
        }
    }
}