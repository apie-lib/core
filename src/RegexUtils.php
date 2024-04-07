<?php
namespace Apie\Core;

use Apie\RegexTools\CompiledRegularExpression;

final class RegexUtils
{
    /**
     * @var array<string, int|null> $alreadyCalculated
     */
    private static array $alreadyCalculated = [];

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

    public static function getMaximumAcceptedStringLengthOfRegularExpression(
        string $regularExpression,
        bool $removeDelimiters = true
    ): ?int {
        if ($removeDelimiters) {
            $regularExpression = self::removeDelimiters($regularExpression);
        }
        if (!isset(self::$alreadyCalculated[$regularExpression])) {
            $regex = CompiledRegularExpression::createFromRegexWithoutDelimiters($regularExpression);
            // regular expression should start with ^ and end with $ to determine max length of an
            // accepted string.
            if (!$regex->hasStartOfRegexMarker() || !$regex->hasEndOfRegexMarker()) {
                return self::$alreadyCalculated[$regularExpression] = null;
            }
            return self::$alreadyCalculated[$regularExpression] = $regex->getMaximumPossibleLength();
        }

        return self::$alreadyCalculated[$regularExpression];
    }
}
