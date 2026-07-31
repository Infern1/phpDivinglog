<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\CountryRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\MediaResolver;
use PhpDivingLog\Support\Seo\DescriptionTruncator;
use PhpDivingLog\Support\UnitConverter;

final readonly class CountryController
{
    public function __construct(
        private CountryRepository $countries,
        private DiveRepository $dives,
        private DiveSiteRepository $sites,
        private MediaResolver $media,
        private Formatter $formatter,
        private UnitConverter $converter,
        private DescriptionTruncator $descriptionTruncator,
    ) {
    }

    /**
     * @return array{countries:list<array<string, mixed>>}
     */
    public function overview(): array
    {
        $rows = $this->countries->listWithDiveCounts();
        $total = count($rows);

        return [
            'countries' => array_map(function (array $row): array {
                $country = $this->mapCountry($row['country']);
                $country['diveCount'] = $row['diveCount'];
                return $country;
            }, $rows),
            'title' => 'Countries',
            'meta_description' => $this->descriptionTruncator->truncate(sprintf(
                'Browse %d %s logged in this logbook, each with its dive sites, flag, and total number of dives.',
                $total,
                $total === 1 ? 'country' : 'countries'
            )),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function detail(int $id): ?array
    {
        $country = $this->countries->findById($id);
        if ($country === null) {
            return null;
        }

        $siteRows = $this->sites->listByCountry($id);
        $diveRows = $this->dives->listOverviewByCountry($id);
        $siteCount = count($siteRows);
        $diveCount = count($diveRows);

        $description = sprintf(
            '%s: %d logged %s across %d dive %s.',
            $country->name,
            $diveCount,
            $diveCount === 1 ? 'dive' : 'dives',
            $siteCount,
            $siteCount === 1 ? 'site' : 'sites'
        );

        return [
            'country' => $this->mapCountry($country),
            'sites' => array_map([$this, 'mapSite'], $siteRows),
            'dives' => $this->mapDiveRows($diveRows),
            'title' => sprintf('%s — Diving Destination', $country->name),
            'meta_description' => $this->descriptionTruncator->truncate($description),
        ];
    }

    /**
     * @param object $country
     * @return array<string, mixed>
     */
    private function mapCountry(object $country): array
    {
        return [
            'id' => $country->id,
            'name' => $country->name,
            'flagUrl' => $country->flagImage !== null ? $this->media->flagUrl($country->flagImage) : null,
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
