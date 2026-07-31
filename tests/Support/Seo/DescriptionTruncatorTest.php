<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support\Seo;

use PhpDivingLog\Support\Seo\DescriptionTruncator;
use PHPUnit\Framework\TestCase;

final class DescriptionTruncatorTest extends TestCase
{
    public function testEmptyStringIsUnchanged(): void
    {
        $truncator = new DescriptionTruncator();

        self::assertSame('', $truncator->truncate(''));
    }

    public function testShortTextUnderLimitIsUnchanged(): void
    {
        $truncator = new DescriptionTruncator();
        $text = 'A short dive description.';

        self::assertSame($text, $truncator->truncate($text, 155));
    }

    public function testTextExactlyAtLimitIsUnchanged(): void
    {
        $truncator = new DescriptionTruncator();
        $text = str_repeat('a', 25);

        self::assertSame($text, $truncator->truncate($text, 25));
    }

    public function testLongTextIsCutAtWordBoundaryWithoutDanglingPunctuation(): void
    {
        $truncator = new DescriptionTruncator();
        $text = 'Dive number five at Nakayukui in Okinawa, Japan on 22 August 2024 with a '
            . 'maximum depth of eighteen point two metres and excellent visibility throughout '
            . 'the whole dive, truly memorable.';

        $result = $truncator->truncate($text, 100);

        self::assertLessThanOrEqual(101, mb_strlen($result, 'UTF-8'));
        self::assertStringEndsWith('…', $result);

        $withoutEllipsis = mb_substr($result, 0, -1, 'UTF-8');
        self::assertNotSame('', $withoutEllipsis);
        self::assertMatchesRegularExpression('/[^\s.,;:!?\-–—]$/u', $withoutEllipsis);

        // Every word before the ellipsis must appear as a whole word in the source text --
        // proof the cut never happened mid-word.
        foreach (explode(' ', $withoutEllipsis) as $word) {
            self::assertStringContainsString($word, $text);
        }
    }

    public function testSingleWordLongerThanLimitFallsBackToBoundedHardCut(): void
    {
        $truncator = new DescriptionTruncator();
        $text = str_repeat('x', 300);

        $result = $truncator->truncate($text, 50);

        self::assertLessThanOrEqual(51, mb_strlen($result, 'UTF-8'));
        self::assertStringEndsWith('…', $result);
    }

    public function testMultibyteTextIsHandledSafely(): void
    {
        $truncator = new DescriptionTruncator();
        $text = str_repeat('é', 60) . ' word ' . str_repeat('ö', 60);

        $result = $truncator->truncate($text, 65);

        self::assertLessThanOrEqual(66, mb_strlen($result, 'UTF-8'));
        self::assertStringEndsWith('…', $result);
    }
}
