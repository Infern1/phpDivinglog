<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\Translator;
use PHPUnit\Framework\TestCase;

final class TranslatorTest extends TestCase
{
    public function testLookupFallbackAndInterpolation(): void
    {
        $translator = new Translator(
            ['hello' => 'Hello {name}', 'fallback_only' => 'Fallback'],
            ['hello' => 'Hi {name}']
        );

        self::assertSame('Hi Diver', $translator->get('hello', ['name' => 'Diver']));
        self::assertSame('Fallback', $translator->get('fallback_only'));
        self::assertSame('missing', $translator->get('missing'));
    }
}
