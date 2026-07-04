<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support;

use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\MediaResolver;
use PHPUnit\Framework\TestCase;

final class MediaResolverTest extends TestCase
{
    public function testResolvesValidRelativePath(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
        ]);

        $resolver = new MediaResolver($config);
        self::assertSame('/images/pictures/27_1.jpg', $resolver->pictureUrl('27_1.jpg'));
    }

    public function testRejectsTraversal(): void
    {
        $config = Config::fromArray([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'user',
        ]);

        $resolver = new MediaResolver($config);
        self::assertSame('/images/icons8-no-image-50.png', $resolver->pictureUrl('../secret.jpg'));
    }
}
