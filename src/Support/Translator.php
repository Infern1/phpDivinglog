<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final class Translator
{
    /**
     * @param array<string, string> $fallback
     * @param array<string, string> $messages
     */
    public function __construct(
        private readonly array $fallback,
        private readonly array $messages = []
    ) {
    }

    /**
     * @param array<string, scalar> $params
     */
    public function get(string $key, array $params = []): string
    {
        $text = $this->messages[$key] ?? $this->fallback[$key] ?? $key;

        if ($params === []) {
            return $text;
        }

        $replacements = [];
        foreach ($params as $name => $value) {
            $replacements['{' . $name . '}'] = (string) $value;
        }

        return strtr($text, $replacements);
    }

    public static function fromFiles(string $language, string $resourceDir): self
    {
        $fallback = self::loadLanguageFile($resourceDir . '/english.php');
        $active = $language === 'english' ? $fallback : self::loadLanguageFile($resourceDir . '/' . $language . '.php');

        return new self($fallback, $active);
    }

    /**
     * @return array<string, string>
     */
    private static function loadLanguageFile(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $data = require $path;
        if (!is_array($data)) {
            return [];
        }

        $messages = [];
        foreach ($data as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $messages[$key] = $value;
            }
        }

        return $messages;
    }
}
