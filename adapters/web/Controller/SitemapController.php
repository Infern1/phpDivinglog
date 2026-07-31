<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\CityRepository;
use PhpDivingLog\Repository\CountryRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Repository\EquipmentRepository;
use PhpDivingLog\Repository\ShopRepository;
use PhpDivingLog\Repository\TripRepository;
use PhpDivingLog\Support\Seo\CanonicalUrlBuilder;

/**
 * Builds an XML sitemap (https://www.sitemaps.org/schemas/sitemap/0.9) covering every indexable
 * overview and detail page, reusing each repository's existing list()/countAll() method and
 * CanonicalUrlBuilder so sitemap entries always match the URLs already declared as canonical
 * elsewhere.
 */
final readonly class SitemapController
{
    /**
     * Upper bound passed to each repository's list() call. Generous relative to any real
     * logbook's site/country/city/shop/trip/equipment counts (unlike dives, these aren't
     * expected to run into the thousands), avoiding the need for a dedicated countAll() on each
     * repository just to size an exact limit.
     */
    private const LIST_LIMIT = 10000;

    public function __construct(
        private DiveRepository $dives,
        private DiveSiteRepository $sites,
        private CountryRepository $countries,
        private CityRepository $cities,
        private ShopRepository $shops,
        private TripRepository $trips,
        private EquipmentRepository $equipment,
        private CanonicalUrlBuilder $canonicalUrlBuilder,
    ) {
    }

    /**
     * @return string|null The full XML document, or null when no public base URL is configured
     *                      (CanonicalUrlBuilder can't produce any URLs).
     */
    public function render(): ?string
    {
        $urls = $this->urls();
        if ($urls === null) {
            return null;
        }

        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        foreach ($urls as $url) {
            $xml[] = '  <url><loc>' . htmlspecialchars($url, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc></url>';
        }
        $xml[] = '</urlset>';

        return implode("\n", $xml) . "\n";
    }

    /**
     * @return list<string>|null
     */
    private function urls(): ?array
    {
        $entries = [
            $this->canonicalUrlBuilder->build('dives.overview', null),
            $this->canonicalUrlBuilder->build('sites.overview', null),
            $this->canonicalUrlBuilder->build('countries.overview', null),
            $this->canonicalUrlBuilder->build('cities.overview', null),
            $this->canonicalUrlBuilder->build('shops.overview', null),
            $this->canonicalUrlBuilder->build('trips.overview', null),
            $this->canonicalUrlBuilder->build('equipment.overview', null),
            $this->canonicalUrlBuilder->build('stats.overview', null),
            $this->canonicalUrlBuilder->build('gallery.overview', null),
        ];

        if ($entries[0] === null) {
            return null;
        }

        foreach ($this->dives->listNumbers($this->dives->countAll(), 0) as $number) {
            $entries[] = $this->canonicalUrlBuilder->build('dives.detail', $number);
        }

        foreach ($this->sites->list(self::LIST_LIMIT) as $site) {
            $entries[] = $this->canonicalUrlBuilder->build('sites.detail', $site->id);
        }

        foreach ($this->countries->list(self::LIST_LIMIT) as $country) {
            $entries[] = $this->canonicalUrlBuilder->build('countries.detail', $country->id);
        }

        foreach ($this->cities->list(self::LIST_LIMIT) as $city) {
            $entries[] = $this->canonicalUrlBuilder->build('cities.detail', $city->id);
        }

        foreach ($this->shops->list(self::LIST_LIMIT) as $shop) {
            $entries[] = $this->canonicalUrlBuilder->build('shops.detail', $shop->id);
        }

        foreach ($this->trips->list(self::LIST_LIMIT) as $trip) {
            $entries[] = $this->canonicalUrlBuilder->build('trips.detail', $trip->id);
        }

        foreach ($this->equipment->list(self::LIST_LIMIT) as $item) {
            $entries[] = $this->canonicalUrlBuilder->build('equipment.detail', $item->id);
        }

        /** @var list<string> */
        return array_values(array_unique(array_filter($entries, static fn (?string $url): bool => $url !== null)));
    }
}
