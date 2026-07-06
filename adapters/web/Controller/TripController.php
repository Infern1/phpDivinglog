<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\TripRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\UnitConverter;

final readonly class TripController
{
    public function __construct(
        private TripRepository $trips,
        private DiveRepository $dives,
        private Formatter $formatter,
        private UnitConverter $converter,
    ) {
    }

    /**
     * @return array{trips:list<array{trip:object,diveCount:int}>}
     */
    public function overview(): array
    {
        $rows = $this->trips->listWithDiveCounts();
        return [
            'trips' => array_map(function (array $row): array {
                return [
                    'trip' => $row['trip'],
                    'diveCount' => $row['diveCount'],
                ];
            }, $rows),
        ];
    }

    /**
     * @return array{trip:object,dives:list<array{number:int,date:string,depth:string,duration:int,location:string,url:string}>}|null
     */
    public function detail(int $id): ?array
    {
        $trip = $this->trips->findById($id);
        if ($trip === null) {
            return null;
        }

        return [
            'trip' => $trip,
            'dives' => $this->mapDiveRows($this->dives->listOverviewByTrip($id)),
        ];
    }

    /**
     * @param list<array{number:int,date_time:\DateTimeImmutable,depth:float,duration:int,location:string}> $rows
     * @return list<array{number:int,date:string,depth:string,duration:int,location:string,url:string}>
     */
    private function mapDiveRows(array $rows): array
    {
        return array_map(function (array $row): array {
            return [
                'number' => $row['number'],
                'date' => $this->formatter->formatDate($row['date_time']),
                'depth' => $this->formatter->formatDecimal($this->converter->depthToDisplay($row['depth']), 1) . ' ' . $this->converter->depthLabel(),
                'duration' => $row['duration'],
                'location' => $row['location'],
                'url' => '/dives/' . $row['number'],
            ];
        }, $rows);
    }
}
