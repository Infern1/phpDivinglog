<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Database;

use PhpDivingLog\Database\Connection;
use PhpDivingLog\Support\ConfigException;
use PHPUnit\Framework\TestCase;

final class ConnectionTest extends TestCase
{
    public function testValidatedTablePrefixAllowsExpectedCharacters(): void
    {
        self::assertSame('DL_42', Connection::validatedTablePrefix('DL_42'));
    }

    public function testValidatedTablePrefixRejectsInvalidCharacters(): void
    {
        $this->expectException(ConfigException::class);

        Connection::validatedTablePrefix('DL-42;DROP');
    }
}
