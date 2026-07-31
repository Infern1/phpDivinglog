<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\PictureRepository;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\MediaResolver;
use PhpDivingLog\Support\Seo\DescriptionTruncator;
use PhpDivingLog\Support\UnitConverter;

final readonly class DiveSiteController
{
    public function __construct(
        private DiveSiteRepository $sites,
        private DiveRepository $dives,
        private PictureRepository $pictures,
        private Formatter $formatter,
        private UnitConverter $converter,
        private MediaResolver $media,
        private DescriptionTruncator $descriptionTruncator
    ) {
    }

    /**
     * @return array{sites:list<array<string, mixed>>}
     */
    public function overview(): array
    {
        $rows = $this->sites->listWithDiveCounts();
        $total = count($rows);

        return [
            'sites' => array_map(function (array $row): array {
                $site = $this->mapSite($row['site']);
                $site['diveCount'] = $row['diveCount'];
                return $site;
            }, $rows),
            'title' => 'Dive Sites',
            'meta_description' => $this->descriptionTruncator->truncate(sprintf(
                'Browse %d dive %s logged in this logbook, each with its dive count, water type, and maximum depth recorded.',
                $total,
                $total === 1 ? 'site' : 'sites'
            )),
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

        $maxDepth = $this->dives->maxDepthByPlace($id);
        $waterTypes = $this->dives->waterTypesByPlace($id);
        $diveRows = $this->dives->listOverviewByPlace($id);
        $pictures = [];

        foreach ($this->dives->listByPlace($id) as $dive) {
            foreach ($this->pictures->findByLogId($dive->logId) as $picture) {
                $pictures[] = [
                    'url' => $this->media->pictureUrl($picture->filename),
                    'thumbUrl' => $this->media->thumbUrl($picture->filename),
                    'description' => $picture->description,
                ];
            }
        }

        $maxDepthDisplay = $maxDepth !== null
            ? $this->formatter->formatDecimal($this->converter->depthToDisplay($maxDepth), 1) . ' ' . $this->converter->depthLabel()
            : null;
        $waterTypesDisplay = $waterTypes !== [] ? implode(' / ', $waterTypes) : null;
        $diveCount = count($diveRows);

        $descriptionParts = [sprintf(
            '%s is a dive site with %d logged %s.',
            $site->name,
            $diveCount,
            $diveCount === 1 ? 'dive' : 'dives'
        )];
        if ($maxDepthDisplay !== null) {
            $descriptionParts[] = sprintf('Maximum depth %s.', $maxDepthDisplay);
        }
        if ($waterTypesDisplay !== null) {
            $descriptionParts[] = sprintf('Water type: %s.', $waterTypesDisplay);
        }

        return [
            'site' => $this->mapSite($site),
            'dives' => $this->mapDiveRows($diveRows),
            'previous_site_id' => $this->sites->findPreviousId($id),
            'next_site_id' => $this->sites->findNextId($id),
            'max_depth_display' => $maxDepthDisplay,
            'water_types_display' => $waterTypesDisplay,
            'pictures' => $pictures,
            'title' => sprintf('%s — Dive Site', $site->name),
            'meta_description' => $this->descriptionTruncator->truncate(implode(' ', $descriptionParts)),
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
