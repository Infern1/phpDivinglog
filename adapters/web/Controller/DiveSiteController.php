<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\MediaResolver;
use PhpDivingLog\Support\UnitConverter;

final readonly class DiveSiteController
{
    public function __construct(
        private DiveSiteRepository $sites,
        private DiveRepository $dives,
        private Formatter $formatter,
        private UnitConverter $converter,
        private MediaResolver $media
    ) {
    }

    /**
     * @return array{sites:list<array<string, mixed>>}
     */
    public function overview(): array
    {
        $rows = $this->sites->listWithDiveCounts();

        return [
            'sites' => array_map(function (array $row): array {
                $site = $this->mapSite($row['site']);
                $site['diveCount'] = $row['diveCount'];
                return $site;
            }, $rows),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function detail(int $id): ?array
    {
        $site = $this->sites->findById($id);
        if ($site === null) {
            return null;
        }

        return [
            'site' => $this->mapSite($site),
            'dives' => $this->mapDiveRows($this->dives->listOverviewByPlace($id)),
        ];
    }

    /**
     * @param object $site
     * @return array<string, mixed>
     */
    private function mapSite(object $site): array
    {
        return [
            'id' => $site->id,
            'name' => $site->name,
            'countryId' => $site->countryId,
            'cityId' => $site->cityId,
            'latitude' => $site->latitude,
            'longitude' => $site->longitude,
            'latitudeFormatted' => $site->latitude !== null ? $this->formatter->formatCoordinate($site->latitude) : null,
            'longitudeFormatted' => $site->longitude !== null ? $this->formatter->formatCoordinate($site->longitude) : null,
            'mapUrl' => $site->mapImage !== null ? $this->media->mapUrl($site->mapImage) : null,
            'comment' => $site->comment,
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
