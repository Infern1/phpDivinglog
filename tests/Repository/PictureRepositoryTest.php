<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Repository;

use PDO;
use PhpDivingLog\Repository\PictureRepository;
use PHPUnit\Framework\TestCase;

final class PictureRepositoryTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE DL_Pictures (PictureID INTEGER PRIMARY KEY, LogID INTEGER, Picture TEXT, Description TEXT)');
        $this->pdo->exec("INSERT INTO DL_Pictures (PictureID, LogID, Picture, Description) VALUES (1, 100, 'dive-100-a.jpg', 'Shark pass')");
        $this->pdo->exec("INSERT INTO DL_Pictures (PictureID, LogID, Picture, Description) VALUES (2, 100, 'dive-100-b.jpg', 'Coral arch')");
        $this->pdo->exec("INSERT INTO DL_Pictures (PictureID, LogID, Picture, Description) VALUES (3, 101, 'dive-101-a.jpg', 'Sunbeams')");
        $this->pdo->exec("INSERT INTO DL_Pictures (PictureID, LogID, Picture, Description) VALUES (4, 102, 'dive-102-a.jpg', 'Drop-off')");
    }

    public function testCountAllReturnsPictureTotal(): void
    {
        $repository = new PictureRepository($this->pdo, 'DL_');

        self::assertSame(4, $repository->countAll());
    }

    public function testFindPageReturnsStableSliceWithOrdering(): void
    {
        $repository = new PictureRepository($this->pdo, 'DL_');

        $firstPage = $repository->findPage(2, 0);
        $secondPage = $repository->findPage(2, 2);

        self::assertCount(2, $firstPage);
        self::assertSame(4, $firstPage[0]->id);
        self::assertSame(102, $firstPage[0]->logId);
        self::assertSame(3, $firstPage[1]->id);
        self::assertSame(101, $firstPage[1]->logId);

        self::assertCount(2, $secondPage);
        self::assertSame(2, $secondPage[0]->id);
        self::assertSame(100, $secondPage[0]->logId);
        self::assertSame(1, $secondPage[1]->id);
        self::assertSame(100, $secondPage[1]->logId);
    }
}
