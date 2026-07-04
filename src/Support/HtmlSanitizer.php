<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final class HtmlSanitizer
{
    public function sanitize(string $html): string
    {
        return strip_tags($html, '<p><br><strong><em><ul><ol><li><a>');
    }
}
