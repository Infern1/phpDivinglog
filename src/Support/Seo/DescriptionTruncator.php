<?php

declare(strict_types=1);

namespace PhpDivingLog\Support\Seo;

/**
 * Enforces a search-result-friendly meta description length, cutting at a word boundary and
 * trimming dangling punctuation rather than chopping mid-word.
 */
final class DescriptionTruncator
{
    private const ELLIPSIS = '…';

    private const TRAILING_PUNCTUATION = " \t\n\r\0\x0B.,;:!?-–—";

    public function truncate(string $text, int $maxLength = 155): string
    {
        $text = trim($text);
        if ($text === '' || mb_strlen($text, 'UTF-8') <= $maxLength) {
            return $text;
        }

        $cut = mb_substr($text, 0, $maxLength, 'UTF-8');
        $lastSpace = mb_strrpos($cut, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $cut = mb_substr($cut, 0, $lastSpace, 'UTF-8');
        }

        $cut = rtrim($cut, self::TRAILING_PUNCTUATION);

        return $cut . self::ELLIPSIS;
    }
}
