<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\MediaResolver;

final readonly class DiveSiteController
{
    public function __construct(
        private DiveSiteRepository $sites,
        private Formatter $formatter,
        private MediaResolver $media
    ) {
    }

    /**
     * @return array{sites:list<array<string, mixed>>}
     */
    public function overview(): array
    {
        return [
            'sites' => array_map([$this, 'mapSite'], $this->sites->list()),
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

        return ['site' => $this->mapSite($site)];
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
}
