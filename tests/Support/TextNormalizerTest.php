<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\TextNormalizer;
use PHPUnit\Framework\TestCase;

final class TextNormalizerTest extends TestCase
{
    public function testNormalUtf8TextStaysUntouched(): void
    {
        self::assertSame('Australië', TextNormalizer::normalizeLikelyMojibake('Australië'));
        self::assertSame('Blue Hole', TextNormalizer::normalizeLikelyMojibake('Blue Hole'));
    }

    public function testMojibakeTextIsReinterpreted(): void
    {
        self::assertSame('Australië', TextNormalizer::normalizeLikelyMojibake('AustraliÃ«'));
        self::assertSame('Bonaire – Curaçao', TextNormalizer::normalizeLikelyMojibake('Bonaire â€“ CuraÃ§ao'));
    }
}
