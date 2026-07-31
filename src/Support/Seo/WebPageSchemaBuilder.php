<?php

declare(strict_types=1);

namespace PhpDivingLog\Support\Seo;

/**
 * Builds a schema.org WebPage JSON-LD payload, safely encoded for embedding in a
 * <script type="application/ld+json"> block.
 */
final class WebPageSchemaBuilder
{
    /**
     * Configured language name (as used by Support\Translator / APP_LANGUAGE) to BCP-47 tag.
     * Extend as new resources/lang/*.php locales are added; an unmapped language simply omits
     * `inLanguage` rather than guessing.
     *
     * @var array<string, string>
     */
    private const LANGUAGE_TAGS = [
        'english' => 'en',
    ];

    public function build(string $canonicalUrl, string $title, ?string $description, string $language): string
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'url' => $canonicalUrl,
            'name' => $title,
        ];

        if ($description !== null && $description !== '') {
            $data['description'] = $description;
        }

        $languageTag = self::LANGUAGE_TAGS[$language] ?? null;
        if ($languageTag !== null) {
            $data['inLanguage'] = $languageTag;
        }

        $json = json_encode(
            $data,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES
        );

        return $json !== false ? $json : '';
    }
}
