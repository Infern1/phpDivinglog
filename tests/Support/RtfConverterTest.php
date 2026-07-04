<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\HtmlSanitizer;
use PhpDivingLog\Support\RtfConverter;
use PHPUnit\Framework\TestCase;

final class RtfConverterTest extends TestCase
{
    public function testConvertsAndSanitizesRtfLikeInput(): void
    {
        $converter = new RtfConverter(new HtmlSanitizer());
        $html = $converter->toHtml('{\\rtf1 Hello \\b world\\b0 <script>alert(1)</script>}');

        self::assertStringContainsString('Hello', $html);
        self::assertStringNotContainsString('<script>', $html);
    }
}
