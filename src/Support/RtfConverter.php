<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final readonly class RtfConverter
{
    public function __construct(private HtmlSanitizer $sanitizer)
    {
    }

    public function toHtml(string $rtf): string
    {
        $plain = preg_replace('/\\\\[a-z]+-?\d* ?/i', '', $rtf);
        $plain = str_replace(['{', '}'], '', (string) $plain);
        $plain = trim($plain);

        if ($plain === '') {
            return '';
        }

        $html = nl2br(htmlspecialchars($plain, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        return $this->sanitizer->sanitize($html);
    }
}
