<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final class TextNormalizer
{
    public static function normalizeLikelyMojibake(string $value): string
    {
        if ($value === '' || !preg_match('/Ã|Â|â/u', $value)) {
            return $value;
        }

        $encodings = ['Windows-1252', 'ISO-8859-1'];
        foreach ($encodings as $encoding) {
            $reinterpreted = iconv('UTF-8', $encoding . '//IGNORE', $value);
            if ($reinterpreted === false || $reinterpreted === '') {
                continue;
            }

            if (mb_check_encoding($reinterpreted, 'UTF-8')) {
                return $reinterpreted;
            }
        }

        return $value;
    }
}
