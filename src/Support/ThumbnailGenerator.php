<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final readonly class ThumbnailGenerator
{
    public function __construct(
        private string $publicRoot,
        private int $targetWidth,
        private int $targetHeight,
    ) {
    }

    public function ensure(string $sourceWebPath, string $thumbWebPath): bool
    {
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagecopyresampled')) {
            return false;
        }

        $sourcePath = $this->toFilesystemPath($sourceWebPath);
        $thumbPath = $this->toFilesystemPath($thumbWebPath);

        if ($sourcePath === null || $thumbPath === null || !is_file($sourcePath)) {
            return false;
        }

        if (is_file($thumbPath)) {
            return true;
        }

        $directory = dirname($thumbPath);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            return false;
        }

        $info = @getimagesize($sourcePath);
        if (!is_array($info)) {
            return false;
        }

        $sourceImage = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/gif' => @imagecreatefromgif($sourcePath),
            default => false,
        };

        if ($sourceImage === false) {
            return false;
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        $scale = min($this->targetWidth / $sourceWidth, $this->targetHeight / $sourceHeight);
        $thumbWidth = max(1, (int) floor($sourceWidth * $scale));
        $thumbHeight = max(1, (int) floor($sourceHeight * $scale));

        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        if ($thumbImage === false) {
            imagedestroy($sourceImage);
            return false;
        }

        if ($info['mime'] === 'image/png' || $info['mime'] === 'image/gif') {
            imagealphablending($thumbImage, false);
            imagesavealpha($thumbImage, true);
            $transparent = imagecolorallocatealpha($thumbImage, 0, 0, 0, 127);
            imagefill($thumbImage, 0, 0, $transparent);
        }

        $resampled = imagecopyresampled(
            $thumbImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $thumbWidth,
            $thumbHeight,
            $sourceWidth,
            $sourceHeight,
        );

        if ($resampled === false) {
            imagedestroy($thumbImage);
            imagedestroy($sourceImage);
            return false;
        }

        $written = match ($info['mime']) {
            'image/jpeg' => imagejpeg($thumbImage, $thumbPath, 85),
            'image/png' => imagepng($thumbImage, $thumbPath, 6),
            'image/gif' => imagegif($thumbImage, $thumbPath),
            default => false,
        };

        imagedestroy($thumbImage);
        imagedestroy($sourceImage);

        return $written && is_file($thumbPath);
    }

    private function toFilesystemPath(string $webPath): ?string
    {
        $trimmed = trim($webPath);
        if ($trimmed === '' || str_contains($trimmed, '..') || str_contains($trimmed, '\\')) {
            return null;
        }

        return rtrim($this->publicRoot, '/\\') . DIRECTORY_SEPARATOR . ltrim($trimmed, '/');
    }
}
