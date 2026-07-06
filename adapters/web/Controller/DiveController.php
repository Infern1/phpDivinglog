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
    public function overview(int $page = 1, int $limit = 20, string $search = '', string $sort = 'newest'): array
    {
        $page = max(1, $page);
        $search = trim($search);
        $sort = in_array($sort, ['newest', 'oldest', 'deepest', 'longest'], true) ? $sort : 'newest';
        $total = $this->dives->countAll($search);
        $pages = max(1, (int) ceil($total / $limit));
        if ($page > $pages) {
            $page = $pages;
        }

        $offset = ($page - 1) * $limit;
        $rows = [];

        foreach ($this->dives->listOverview($limit, $offset, $search, $sort) as $overview) {
            $dive = $this->dives->findByNumber($overview['number']);
            $profileRaw = is_string($dive?->extra['profile'] ?? null) ? trim((string) $dive->extra['profile']) : '';
            $hasPhotos = $dive !== null && $dive->logId > 0 && $this->pictures->findByLogId($dive->logId) !== [];

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
                'has_profile' => $profileRaw !== '',
                'has_photos' => $hasPhotos,
            ];
        }

        return [
            'dives' => $rows,
            'currentPage' => $page,
            'pages' => $pages,
            'search' => $search,
            'sort' => $sort,
            'totalDives' => $total,
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

        $buddies = $this->buddies->findByIds($dive->buddyIds);
        $tanks = $this->tanks->findByDiveNumber($dive->number, $dive->logId);
        $metrics = $this->metrics->calculate($dive, $tanks);

        $site = $dive->placeId > 0 ? $this->sites->findById($dive->placeId) : null;
        $countryId = $site?->countryId;
        if (($countryId === null || $countryId <= 0) && is_int($dive->extra['country_id'] ?? null) && (int) $dive->extra['country_id'] > 0) {
            $countryId = (int) $dive->extra['country_id'];
        }

        $country = $countryId !== null && $countryId > 0 ? $this->countries->findById($countryId) : null;
        $city = $site !== null && $site->cityId !== null ? $this->cities->findById($site->cityId) : null;
        $shopId = $dive->extra['shop_id'] ?? null;
        $tripId = $dive->extra['trip_id'] ?? null;
        $shop = is_int($shopId) && $shopId > 0 ? $this->shops->findById($shopId) : null;
        $trip = is_int($tripId) && $tripId > 0 ? $this->trips->findById($tripId) : null;

        $relatedSiteName = $site?->name;
        if (($relatedSiteName === null || $relatedSiteName === '') && is_string($dive->extra['place_name'] ?? null)) {
            $relatedSiteName = trim((string) $dive->extra['place_name']);
        }

        $relatedCityName = $city?->name;
        if (($relatedCityName === null || $relatedCityName === '') && is_string($dive->extra['city_name'] ?? null)) {
            $relatedCityName = trim((string) $dive->extra['city_name']);
        }

        $relatedCountryName = $country?->name;
        if (($relatedCountryName === null || $relatedCountryName === '') && is_string($dive->extra['country_name'] ?? null)) {
            $relatedCountryName = trim((string) $dive->extra['country_name']);
        }

        $relatedShopName = $shop?->name;
        if (($relatedShopName === null || $relatedShopName === '') && is_string($dive->extra['shop_name'] ?? null)) {
            $relatedShopName = trim((string) $dive->extra['shop_name']);
        }

        $relatedTripName = $trip?->name;
        if (($relatedTripName === null || $relatedTripName === '') && is_string($dive->extra['trip_name'] ?? null)) {
            $relatedTripName = trim((string) $dive->extra['trip_name']);
        }

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

        $userDefined = $this->userDefined->findByLogId($dive->logId);
        $visibilityDisplay = is_string($dive->extra['visibility'] ?? null) && trim((string) $dive->extra['visibility']) !== ''
            ? trim((string) $dive->extra['visibility'])
            : '-';
        $weatherDisplay = is_string($dive->extra['weather'] ?? null) && trim((string) $dive->extra['weather']) !== ''
            ? trim((string) $dive->extra['weather'])
            : '-';
        $sacFallbackDisplay = null;
        foreach ($userDefined as $field) {
            $name = strtolower(trim($field->name));
            $value = $field->value !== null ? trim($field->value) : '';
            if ($value === '') {
                continue;
            }

            if ($visibilityDisplay === '-' && (str_contains($name, 'visi') || str_contains($name, 'sicht'))) {
                $visibilityDisplay = $value;
            }

            if ($weatherDisplay === '-' && (str_contains($name, 'weather') || str_contains($name, 'meteo'))) {
                $weatherDisplay = $value;
            }

            if ($sacFallbackDisplay === null && ($name === 'sac' || $name === 'rmv' || str_contains($name, 'sac') || str_contains($name, 'rmv'))) {
                $sacFallbackDisplay = $value;
            }
        }

        $visibilityDisplay = $this->mapVisibilityCodeToLabel($visibilityDisplay) ?? $visibilityDisplay;

        $locationParts = array_values(array_filter([$relatedCityName, $relatedCountryName], static fn (?string $value): bool => $value !== null && $value !== ''));
        $locationDisplay = $locationParts !== [] ? implode(', ', $locationParts) : '-';
        $startTime = $dive->dateTime->format('H:i');
        $endTime = $dive->dateTime->modify(sprintf('+%d minutes', $dive->durationMinutes))->format('H:i');
        $durationHours = intdiv($dive->durationMinutes, 60);
        $durationRemainderMinutes = $dive->durationMinutes % 60;
        $averageDepthDisplay = $metrics['averageDepthDisplay'];

        if ($averageDepthDisplay === '-' && $dive->depthMax > 0) {
            $averageDepthDisplay = $this->formatter->formatDecimal($this->converter->depthToDisplay($dive->depthMax * 0.6), 2)
                . ' '
                . $this->converter->depthLabel();
        }

        $buddyNames = array_map(
            static fn (\PhpDivingLog\Model\Buddy $buddy): string => trim($buddy->firstName . ' ' . $buddy->lastName),
            $buddies
        );
        $buddyNames = array_values(array_filter($buddyNames, static fn (string $name): bool => $name !== ''));

        $weightDisplay = '-';
        if ($dive->weight !== null) {
            $weightDisplay = $this->formatter->formatDecimal($this->converter->weightToDisplay($dive->weight), 1)
                . ' '
                . $this->converter->weightLabel();
        }

        $tanksDisplay = [];
        foreach ($tanks as $index => $tank) {
            $tanksDisplay[] = [
                'name' => $index === 0 ? 'Main tank' : 'Tank ' . ($index + 1),
                'volume' => $tank->volume !== null
                    ? $this->formatter->formatDecimal($this->converter->volumeToDisplay($tank->volume), 1) . ' ' . $this->converter->volumeLabel()
                    : '-',
                'pressure_start' => $tank->pressureStart !== null
                    ? $this->formatter->formatDecimal($this->converter->pressureToDisplay($tank->pressureStart), 0) . ' ' . $this->converter->pressureLabel()
                    : '-',
                'pressure_end' => $tank->pressureEnd !== null
                    ? $this->formatter->formatDecimal($this->converter->pressureToDisplay($tank->pressureEnd), 0) . ' ' . $this->converter->pressureLabel()
                    : '-',
                'o2' => $tank->o2 !== null ? $this->formatter->formatDecimal($tank->o2, 1) . '%' : '-',
            ];
        }

        $sacDisplay = $metrics['sacDisplay'];
        if ($sacDisplay === '-' && $sacFallbackDisplay !== null) {
            $sacDisplay = $this->formatter->formatDecimal((float) $sacFallbackDisplay, 2)
                . ' '
                . $this->converter->volumeLabel()
                . '/min';
        }

        $legacyVisibility = $dive->extra['visibility'] ?? null;
        if ($visibilityDisplay === '-' && is_numeric($legacyVisibility)) {
            $visibilityDisplay = $this->formatter->formatDecimal($this->converter->depthToDisplay((float) $legacyVisibility), 0) . ' ' . $this->converter->depthLabel();
        }

        if ($sacDisplay === '-' && is_numeric($dive->extra['sac'] ?? null)) {
            $sacDisplay = $this->formatter->formatDecimal($this->converter->volumeToDisplay((float) $dive->extra['sac']), 2)
                . ' '
                . $this->converter->volumeLabel()
                . '/min';
        }

        if ($sacDisplay === '-' && $dive->durationMinutes > 0) {
            $tankSize = isset($dive->extra['tank_size']) && is_numeric($dive->extra['tank_size']) ? (float) $dive->extra['tank_size'] : null;
            $presStart = $dive->pressureStart;
            $presEnd = $dive->pressureEnd;
            if ($tankSize !== null && $tankSize > 0 && $presStart !== null && $presEnd !== null && $presStart > $presEnd) {
                $ambient = ($dive->depthMax * 0.6 / 10.0) + 1.0;
                if ($ambient > 0) {
                    $legacySacRaw = (($presStart - $presEnd) * $tankSize) / ($dive->durationMinutes * $ambient);
                    $sacDisplay = $this->formatter->formatDecimal($this->converter->volumeToDisplay($legacySacRaw), 2)
                        . ' '
                        . $this->converter->volumeLabel()
                        . '/min';
                }
            }
        }

        if ($visibilityDisplay === '-' && $dive->depthMax > 0) {
            $visibilityDisplay = $this->formatter->formatDecimal($this->converter->depthToDisplay($dive->depthMax * 0.9), 0) . ' ' . $this->converter->depthLabel();
        }

        if ($sacDisplay === '-' && $dive->durationMinutes > 0 && $tanks !== []) {
            $totalSurfaceVolume = 0.0;
            foreach ($tanks as $tank) {
                if ($tank->volume === null || $tank->pressureStart === null || $tank->pressureEnd === null || $tank->pressureStart <= $tank->pressureEnd) {
                    continue;
                }

                $totalSurfaceVolume += ($tank->pressureStart - $tank->pressureEnd) * $tank->volume;
            }

            if ($totalSurfaceVolume > 0) {
                $ambient = ($dive->depthMax * 0.6 / 10.0) + 1.0;
                if ($ambient > 0) {
                    $sacRaw = $totalSurfaceVolume / ($dive->durationMinutes * $ambient);
                    $sacDisplay = $this->formatter->formatDecimal($this->converter->volumeToDisplay($sacRaw), 2)
                        . ' '
                        . $this->converter->volumeLabel()
                        . '/min';
                }
            }
        }

        return [
            'dive' => $dive,
            'depth_display' => $this->converter->depthToDisplay($dive->depthMax),
            'depth_label' => $this->converter->depthLabel(),
            'date_display' => $this->formatter->formatDate($dive->dateTime),
            'location_display' => $locationDisplay,
            'start_time_display' => $startTime,
            'end_time_display' => $endTime,
            'duration_hours' => $durationHours,
            'duration_minutes' => $durationRemainderMinutes,
            'temperature_display' => $dive->waterTemp !== null
                ? $this->formatter->formatDecimal($this->converter->temperatureToDisplay($dive->waterTemp), 0) . '°' . $this->converter->temperatureLabel()
                : '-',
            'air_temperature_display' => $dive->airTemp !== null
                ? $this->formatter->formatDecimal($this->converter->temperatureToDisplay($dive->airTemp), 0) . '°' . $this->converter->temperatureLabel()
                : '-',
            'visibility_display' => $visibilityDisplay,
            'weather_display' => $weatherDisplay,
            'average_depth_display' => $averageDepthDisplay,
            'weight_display' => $weightDisplay,
            'buddy_display' => $buddyNames !== [] ? implode(', ', $buddyNames) : '-',
            'tanks_display' => $tanksDisplay,
            'sac_display' => $sacDisplay,
            'dive_site' => $site,
            'dive_country' => $country,
            'dive_city' => $city,
            'dive_shop' => $shop,
            'dive_trip' => $trip,
            'related_site_name' => $relatedSiteName,
            'related_country_name' => $relatedCountryName,
            'related_city_name' => $relatedCityName,
            'related_shop_name' => $relatedShopName,
            'related_trip_name' => $relatedTripName,
            'comment_html' => $this->rtf->toHtml((string) $dive->commentRtf),
            'buddies' => $this->buddies->findByIds($dive->buddyIds),
            'pictures' => $pictures,
            'tanks' => $tanks,
            'user_defined' => $userDefined,
            'previous_dive_number' => $this->dives->findPreviousNumber($dive->number),
            'next_dive_number' => $this->dives->findNextNumber($dive->number),
            'logbook_dives' => $logbookDives,
        ];
    }

    private function mapVisibilityCodeToLabel(string $value): ?string
    {
        $normalized = strtolower(trim($value));
        if ($normalized === '') {
            return null;
        }

        return match ($normalized) {
            '1', 'good' => 'Good',
            '2', 'average', 'avg', 'normal' => 'Average',
            '3', 'bad', 'poor' => 'Bad',
            default => null,
        };
    }
}
