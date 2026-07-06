<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\PersonalRepository;
use PhpDivingLog\Repository\PictureRepository;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\MediaResolver;

final readonly class GalleryController
{
    public function __construct(
        private PictureRepository $pictures,
        private MediaResolver $media,
        private DiveRepository $dives,
        private PersonalRepository $personal,
        private Formatter $formatter,
    ) {
    }

    /**
     * @return array{pictures:list<array<string, mixed>>}
     */
    public function forDive(int $logId): array
    {
        $pictures = array_map(
            fn ($picture) => [
                'id' => $picture->id,
                'url' => $this->media->pictureUrl($picture->filename),
                'thumb' => $this->media->thumbUrl($picture->filename),
                'description' => $picture->description,
            ],
            $this->pictures->findByLogId($logId)
        );

        return ['pictures' => $pictures];
    }

    /**
     * @return array{pictures:list<array<string, mixed>>,currentPage:int,pages:int,total:int}
     */
    public function overview(int $page = 1, int $perPage = 24): array
    {
        $total = $this->pictures->countAll();
        $pages = max(1, (int) ceil($total / max(1, $perPage)));
        $currentPage = min(max(1, $page), $pages);
        $offset = ($currentPage - 1) * $perPage;

        $pictureModels = $this->pictures->findPage($perPage, $offset);
        $logIds = array_values(array_unique(array_map(static fn ($picture): int => $picture->logId, $pictureModels)));
        $metaByLogId = $this->dives->findMetaByLogIds($logIds);

        $profile = $this->personal->getProfile();
        $diverName = trim(($profile?->firstName ?? '') . ' ' . ($profile?->lastName ?? ''));

        $pictures = array_map(function ($picture) use ($metaByLogId, $diverName): array {
            $meta = $metaByLogId[$picture->logId] ?? null;
            $when = '';

            if (is_array($meta)) {
                $when = trim($this->formatter->formatDate($meta['date_time']) . ' ' . $meta['date_time']->format('H:i'));
            }

            $locationParts = [];
            if (is_array($meta)) {
                $countryName = trim((string) ($meta['country_name'] ?? ''));
                $cityName = trim((string) ($meta['city_name'] ?? ''));
                if ($countryName !== '') {
                    $locationParts[] = $countryName;
                }
                if ($cityName !== '') {
                    $locationParts[] = $cityName;
                }
            }

            $location = implode(', ', $locationParts);
            $site = is_array($meta) ? trim((string) ($meta['place_name'] ?? '')) : '';
            $diveNumber = is_array($meta) && $meta['number'] > 0 ? $meta['number'] : null;

            return [
                'id' => $picture->id,
                'url' => $this->media->pictureUrl($picture->filename),
                'thumb' => $this->media->thumbUrl($picture->filename),
                'description' => $picture->description,
                'diveNumber' => $diveNumber,
                'diver' => $diverName,
                'location' => $location,
                'site' => $site,
                'when' => $when,
                'diveUrl' => $diveNumber !== null ? '/dives/' . $diveNumber : '',
            ];
        }, $pictureModels);

        return [
            'pictures' => $pictures,
            'currentPage' => $currentPage,
            'pages' => $pages,
            'total' => $total,
        ];
    }
}
