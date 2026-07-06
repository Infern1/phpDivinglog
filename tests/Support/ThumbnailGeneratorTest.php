<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\ThumbnailGenerator;
use PHPUnit\Framework\TestCase;

final class ThumbnailGeneratorTest extends TestCase
{
    private string $publicRoot;

    protected function setUp(): void
    {
        $this->publicRoot = sys_get_temp_dir() . '/phpdivinglog-thumb-' . bin2hex(random_bytes(6));
        mkdir($this->publicRoot . '/images/pictures', 0775, true);
    }

    protected function tearDown(): void
    {
        $this->deleteRecursive($this->publicRoot);
    }

    public function testGeneratesThumbnailFromSourceImage(): void
    {
        if (!function_exists('imagecreatetruecolor')) {
            self::markTestSkipped('GD extension is required for thumbnail generation tests.');
        }

        $sourcePath = $this->publicRoot . '/images/pictures/source.jpg';
        $image = imagecreatetruecolor(240, 120);
        self::assertNotFalse($image);
        $color = imagecolorallocate($image, 0, 128, 255);
        imagefill($image, 0, 0, $color);
        imagejpeg($image, $sourcePath, 85);
        imagedestroy($image);

        $generator = new ThumbnailGenerator($this->publicRoot, 100, 75);

        $result = $generator->ensure('/images/pictures/source.jpg', '/images/pictures/thumb/source.jpg');

        self::assertTrue($result);
        self::assertFileExists($this->publicRoot . '/images/pictures/thumb/source.jpg');
    }

    private function deleteRecursive(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $entries = scandir($path);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $path . '/' . $entry;
            if (is_dir($fullPath)) {
                $this->deleteRecursive($fullPath);
                continue;
            }

            @unlink($fullPath);
        }

        @rmdir($path);
    }
}
