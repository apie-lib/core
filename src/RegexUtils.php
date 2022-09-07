<?php
namespace Apie\Core;

final class RegexUtils
{
    private function __construct()
    {
    }

    public static function removeDelimiters(string $regularExpressionWithDelimiter): string
    {
        $delimiter = preg_quote(substr($regularExpressionWithDelimiter, 0, 1), '#');
        $removeStartDelimiterRegex = '#^' . $delimiter . '#u';
        $regex = preg_replace($removeStartDelimiterRegex, '', $regularExpressionWithDelimiter);
        $removeEndDelimiterRegex = '#' . $delimiter . '[imsxADSUJXu]*$#u';
        return  preg_replace($removeEndDelimiterRegex, '', $regex);
    }
}
