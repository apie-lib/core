<?php
namespace Apie\Core\Utils;

final class LanguageParser
{
    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @return string[]
     */
    public static function parseLanguageHeader(string $headerValue): array
    {
        $res = [];
        foreach (explode(',', $headerValue) as $str) {
            $parts = explode(';', $str);
            $locale = trim($parts[0]);
            $priority = 1.0;
            foreach ($parts as $part) {
                $part = trim($part);
                if (str_starts_with($part, 'q=')) {
                    $priority = (float) substr($part, 2);
                }
            }
            if ($locale) {
                $res[$locale] = $priority;
            }
        }
        arsort($res);
        return array_keys($res);
    }
}
