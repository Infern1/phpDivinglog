<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\BuddyRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Repository\PictureRepository;
use PhpDivingLog\Repository\CountryRepository;
use PhpDivingLog\Repository\CityRepository;
use PhpDivingLog\Repository\ShopRepository;
use PhpDivingLog\Repository\TripRepository;
use PhpDivingLog\Repository\TankRepository;
use PhpDivingLog\Repository\UserDefinedRepository;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\DiveMetricsCalculator;
use PhpDivingLog\Support\MediaResolver;
use PhpDivingLog\Support\RtfConverter;
use PhpDivingLog\Support\UnitConverter;

final readonly class DiveController
{
    public function __construct(
        private DiveRepository $dives,
        private BuddyRepository $buddies,
        private PictureRepository $pictures,
        private DiveSiteRepository $sites,
        private CountryRepository $countries,
        private CityRepository $cities,
        private ShopRepository $shops,
        private TripRepository $trips,
        private TankRepository $tanks,
        private UserDefinedRepository $userDefined,
        private UnitConverter $converter,
        private Formatter $formatter,
        private DiveMetricsCalculator $metrics,
        private RtfConverter $rtf,
        private MediaResolver $media
    ) {
    }

    /**
     * @return array{dives:list<array<string, mixed>>}
     */
    public function overview(int $page = 1, int $limit = 20): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $limit;
        $rows = [];

        foreach ($this->dives->listOverview($limit, $offset) as $overview) {
            $rows[] = [
                'number' => $overview['number'],
                'date' => $this->formatter->formatDate($overview['date_time']),
                'time' => $overview['date_time']->format('H:i'),
                'depth' => number_format($this->converter->depthToDisplay($overview['depth']), 1, ',', ''),
                'depth_value' => round($this->converter->depthToDisplay($overview['depth']), 2),
                'depth_label' => $this->converter->depthLabel(),
                'duration' => $overview['duration'],
                'timestamp' => $overview['date_time']->getTimestamp(),
                'location' => $overview['location'],
            ];
        }

        $total = $this->dives->countAll();
        $pages = max(1, (int) ceil($total / $limit));

        return [
            'dives' => $rows,
            'currentPage' => $page,
            'pages' => $pages,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function detail(int $number): ?array
    {
        $dive = $this->dives->findByNumber($number);
        if ($dive === null) {
            return null;
        }

        $pictures = array_map(
            fn ($picture) => [
                'id' => $picture->id,
                'url' => $this->media->pictureUrl($picture->filename),
                'thumbUrl' => $this->media->thumbUrl($picture->filename),
                'description' => $picture->description,
            ],
            $this->pictures->findByLogId($dive->logId)
        );

        $tanks = $this->tanks->findByDiveNumber($dive->number);
        $metrics = $this->metrics->calculate($dive, $tanks);

        $site = $dive->placeId > 0 ? $this->sites->findById($dive->placeId) : null;
        $country = $site !== null && $site->countryId !== null ? $this->countries->findById($site->countryId) : null;
        $city = $site !== null && $site->cityId !== null ? $this->cities->findById($site->cityId) : null;
        $shopId = $dive->extra['shop_id'] ?? null;
        $tripId = $dive->extra['trip_id'] ?? null;
        $shop = is_int($shopId) && $shopId > 0 ? $this->shops->findById($shopId) : null;
        $trip = is_int($tripId) && $tripId > 0 ? $this->trips->findById($tripId) : null;

        $logbookDives = array_map(
            function (array $overview) use ($dive): array {
                return [
                    'number' => $overview['number'],
                    'date' => $this->formatter->formatDate($overview['date_time']),
                    'depth' => number_format($this->converter->depthToDisplay($overview['depth']), 1, ',', ''),
                    'depth_label' => $this->converter->depthLabel(),
                    'duration' => $overview['duration'],
                    'location' => $overview['location'],
                    'active' => $overview['number'] === $dive->number,
                ];
            },
            $this->dives->listOverview(200, 0)
        );

        return [
            'dive' => $dive,
            'depth_display' => $this->converter->depthToDisplay($dive->depthMax),
            'depth_label' => $this->converter->depthLabel(),
            'date_display' => $this->formatter->formatDate($dive->dateTime),
            'average_depth_display' => $metrics['averageDepthDisplay'],
            'sac_display' => $metrics['sacDisplay'],
            'dive_site' => $site,
            'dive_country' => $country,
            'dive_city' => $city,
            'dive_shop' => $shop,
            'dive_trip' => $trip,
            'comment_html' => $this->rtf->toHtml((string) $dive->commentRtf),
            'buddies' => $this->buddies->findByIds($dive->buddyIds),
            'pictures' => $pictures,
            'tanks' => $tanks,
            'user_defined' => $this->userDefined->findByLogId($dive->logId),
            'previous_dive_number' => $this->dives->findPreviousNumber($dive->number),
            'next_dive_number' => $this->dives->findNextNumber($dive->number),
            'logbook_dives' => $logbookDives,
        ];
    }
}
