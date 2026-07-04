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
     * @return array{numbers:list<int>}
     */
    public function overview(int $limit = 20, int $offset = 0): array
    {
        return [
            'numbers' => $this->dives->listNumbers($limit, $offset),
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
        ];
    }
}
