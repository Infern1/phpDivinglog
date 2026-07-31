<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support\Seo;

use PhpDivingLog\Support\Seo\WebPageSchemaBuilder;
use PHPUnit\Framework\TestCase;

final class WebPageSchemaBuilderTest extends TestCase
{
    public function testIncludesRequiredKeysAndOptionalFields(): void
    {
        $builder = new WebPageSchemaBuilder();

        $json = $builder->build(
            'https://dives.example.com/dives/5',
            'Dive #5',
            'A great dive.',
            'english'
        );

        $decoded = json_decode($json, true);

        self::assertSame('https://schema.org', $decoded['@context']);
        self::assertSame('WebPage', $decoded['@type']);
        self::assertSame('https://dives.example.com/dives/5', $decoded['url']);
        self::assertSame('Dive #5', $decoded['name']);
        self::assertSame('A great dive.', $decoded['description']);
        self::assertSame('en', $decoded['inLanguage']);
    }

    public function testOmitsDescriptionWhenNull(): void
    {
        $builder = new WebPageSchemaBuilder();

        $decoded = json_decode(
            $builder->build('https://dives.example.com/', 'All Dives', null, 'english'),
            true
        );

        self::assertArrayNotHasKey('description', $decoded);
    }

    public function testOmitsDescriptionWhenEmptyString(): void
    {
        $builder = new WebPageSchemaBuilder();

        $decoded = json_decode(
            $builder->build('https://dives.example.com/', 'All Dives', '', 'english'),
            true
        );

        self::assertArrayNotHasKey('description', $decoded);
    }

    public function testOmitsInLanguageForUnmappedLanguage(): void
    {
        $builder = new WebPageSchemaBuilder();

        $decoded = json_decode(
            $builder->build('https://dives.example.com/', 'All Dives', 'desc', 'klingon'),
            true
        );

        self::assertArrayNotHasKey('inLanguage', $decoded);
    }

    public function testEncodingIsSafeAgainstScriptInjectionAndRoundTrips(): void
    {
        $builder = new WebPageSchemaBuilder();
        $evil = '</script><script>alert(1)</script> and "quotes" and \'apostrophes\'';

        $json = $builder->build('https://dives.example.com/dives/6', $evil, $evil, 'english');

        self::assertStringNotContainsString('</script>', $json);

        $decoded = json_decode($json, true);
        self::assertSame($evil, $decoded['name']);
        self::assertSame($evil, $decoded['description']);
    }
}
