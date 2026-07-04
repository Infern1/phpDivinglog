<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final readonly class MediaResolver
{
    public function __construct(private Config $config)
    {
    }

    public function pictureUrl(string $filename): string
    {
        return $this->resolveRelativePath($this->config->picPathWeb(), $filename);
    }

    public function thumbUrl(string $filename): string
    {
        return $this->resolveRelativePath($this->config->picPathWebThumb(), $filename);
    }

    public function mapUrl(string $filename): string
    {
        return $this->resolveRelativePath($this->config->mapPathWeb(), $filename);
    }

    public function flagUrl(string $filename): string
    {
        return $this->resolveRelativePath($this->config->flagPathWeb(), $filename);
    }

    public function equipmentUrl(string $filename): string
    {
        return $this->resolveRelativePath($this->config->equipPathWeb(), $filename);
    }

    private function resolveRelativePath(string $base, string $filename): string
    {
        $cleanFilename = trim($filename);
        if ($cleanFilename === '' || str_contains($cleanFilename, '..') || str_contains($cleanFilename, '\\')) {
            return $this->config->picMissing();
        }

        $base = rtrim($base, '/') . '/';

        return $base . ltrim($cleanFilename, '/');
    }
}
