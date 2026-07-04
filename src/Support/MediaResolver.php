<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final readonly class MediaResolver
{
    public function __construct(
        private Config $config,
        private ?ThumbnailGenerator $thumbnailGenerator = null,
    ) {
    }

    public function pictureUrl(string $filename): string
    {
        return $this->resolveRelativePath($this->config->picPathWeb(), $filename);
    }

    public function thumbUrl(string $filename): string
    {
        $source = $this->resolveRelativePath($this->config->picPathWeb(), $filename);
        if ($source === $this->config->picMissing()) {
            return $source;
        }

        $thumb = $this->resolveRelativePath($this->config->picPathWebThumb(), $filename);
        if ($thumb === $this->config->picMissing()) {
            return $source;
        }

        if ($this->thumbnailGenerator === null) {
            return $thumb;
        }

        if ($this->thumbnailGenerator->ensure($source, $thumb)) {
            return $thumb;
        }

        return $source;
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
