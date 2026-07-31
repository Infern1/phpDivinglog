<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Http;

use PhpDivingLog\Support\Config;
use PHPUnit\Framework\TestCase;

final class WebSmokeTest extends TestCase
{
    private int $initialOutputBufferLevel = 0;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $this->seedFixtureDatabase();
        $this->initialOutputBufferLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->initialOutputBufferLevel) {
            ob_end_clean();
        }
    }

    public function testHomePageRendersDiveOverview(): void
    {
        $response = $this->request('/');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('dives', $response['body']);
        self::assertStringContainsString('data-dives-table', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
        self::assertStringContainsString('role="link"', $response['body']);
        self::assertStringContainsString('Search location, number...', $response['body']);
        self::assertStringContainsString('name="q"', $response['body']);
        self::assertStringContainsString('name="sort"', $response['body']);
        self::assertStringContainsString('show_chart', $response['body']);
        self::assertStringContainsString('photo_camera', $response['body']);
        self::assertStringContainsString('Apply', $response['body']);
        self::assertStringContainsString('/assets/vendor/beercss/beer.min.css', $response['body']);
        self::assertStringContainsString('/assets/vendor/beercss/material-dynamic-colors.min.js', $response['body']);
        self::assertStringContainsString('/assets/js/theme.js', $response['body']);
        self::assertStringContainsString('data-theme-toggle', $response['body']);
        self::assertStringContainsString('Robin Diver Dive Log', $response['body']);
        self::assertStringContainsString('data-palette-toggle', $response['body']);
        self::assertStringContainsString('divelog:palette', $response['body']);
        self::assertStringContainsString('data-palette', $response['body']);
    }

    public function testDiveDetailRendersContent(): void
    {
        $response = $this->request('/dives/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive #1', $response['body']);
        self::assertStringContainsString('dive-hero-wide', $response['body']);
        self::assertStringContainsString('Depth', $response['body']);
        self::assertStringContainsString('Duration', $response['body']);
        self::assertStringContainsString('Temp', $response['body']);
        self::assertStringContainsString('Visibility', $response['body']);
        self::assertStringContainsString('Avg depth', $response['body']);
        self::assertStringContainsString('Weather', $response['body']);
        self::assertStringContainsString('Weight', $response['body']);
        self::assertStringContainsString('Buddy', $response['body']);
        self::assertStringContainsString('Tanks', $response['body']);
        self::assertStringContainsString('Main tank', $response['body']);
        self::assertStringContainsString('Press. Start', $response['body']);
        self::assertStringContainsString('Details', $response['body']);
        self::assertStringContainsString('Dive site', $response['body']);
        self::assertStringContainsString('Location', $response['body']);
        self::assertStringContainsString('Country', $response['body']);
        self::assertStringContainsString('/countries/1', $response['body']);
        self::assertStringContainsString('<dt>Country</dt>', $response['body']);
        self::assertStringContainsString('Temp. Air', $response['body']);
        self::assertStringContainsString('Blue Hole', $response['body']);
        self::assertStringContainsString('Ocean Dive Center', $response['body']);
        self::assertStringContainsString('Spring Bahamas', $response['body']);
        self::assertStringContainsString('Dive profile', $response['body']);
        self::assertStringContainsString('profile-chart', $response['body']);
        self::assertStringContainsString('data-profile-live="depth"', $response['body']);
        self::assertStringNotContainsString('Ascent / descent rates', $response['body']);
        self::assertStringNotContainsString('profile-rate-chart', $response['body']);
        self::assertStringNotContainsString('data-profile-live="rate"', $response['body']);
        self::assertStringContainsString('Logbook', $response['body']);
        self::assertStringContainsString('data-logbook-pane', $response['body']);
        self::assertStringContainsString('data-logbook-list', $response['body']);
        self::assertStringContainsString('data-logbook-link', $response['body']);
        self::assertStringContainsString('dive-sequence-nav-top', $response['body']);
        self::assertStringContainsString('aria-label="Next dive"', $response['body']);
        self::assertStringContainsString('class="gallery-grid" data-lightbox-group="dive-pictures"', $response['body']);
        self::assertStringContainsString('/assets/js/lightbox.js', $response['body']);
        self::assertStringContainsString('/assets/js/profile-chart.js', $response['body']);
    }

    public function testUnknownDiveReturnsNotFound(): void
    {
        $response = $this->request('/dives/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
        self::assertStringContainsString('<meta name="robots" content="noindex,nofollow">', $response['body']);
    }

    public function testDiveDetailFullIncludesNavScript(): void
    {
        $response = $this->request('/dives/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('<!doctype html>', $response['body']);
        self::assertStringContainsString('/assets/js/dive-detail-nav.js', $response['body']);
        self::assertStringContainsString('data-dive-fragment', $response['body']);
    }

    public function testDiveDetailPartialReturnsFragmentOnly(): void
    {
        $response = $this->request('/dives/1', ['X-Requested-With' => 'XMLHttpRequest']);

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('data-dive-fragment', $response['body']);
        self::assertStringContainsString('data-dive-number="1"', $response['body']);
        self::assertStringContainsString('dive-content-column', $response['body']);
        self::assertStringNotContainsString('<!doctype html>', $response['body']);
        self::assertStringNotContainsString('Primary navigation', $response['body']);
        self::assertStringNotContainsString('/assets/js/dive-detail-nav.js', $response['body']);
    }

    public function testDiveDetailPartialUnknownReturnsNotFound(): void
    {
        $response = $this->request('/dives/9999', ['X-Requested-With' => 'XMLHttpRequest']);

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
    }

    public function testSitesOverviewRenders(): void
    {
        $response = $this->request('/sites');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Sites', $response['body']);
        self::assertStringContainsString('Blue Hole', $response['body']);
        self::assertStringContainsString('data-href="/sites/10"', $response['body']);
        self::assertStringContainsString('<td>2</td>', $response['body']);
    }

    public function testSiteDetailRenders(): void
    {
        $response = $this->request('/sites/10');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Blue Hole', $response['body']);
        self::assertStringContainsString('Dives at this site', $response['body']);
        self::assertStringContainsString('aria-label="Site sequence navigation"', $response['body']);
        self::assertStringContainsString('aria-label="Next site"', $response['body']);
        self::assertStringContainsString('/sites/11', $response['body']);
        self::assertStringContainsString('dive-sequence-link is-disabled', $response['body']);
        self::assertStringContainsString('Open in Google Maps', $response['body']);
        self::assertStringContainsString('Max depth:', $response['body']);
        self::assertStringContainsString('Water:', $response['body']);
        self::assertStringContainsString('href="/images/maps/blue-hole-map.jpg" data-lightbox', $response['body']);
        self::assertStringContainsString('data-lightbox', $response['body']);
        self::assertStringContainsString('/assets/js/lightbox.js', $response['body']);
        self::assertStringContainsString('dive-100-a.jpg', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
        self::assertStringContainsString('data-href="/dives/3"', $response['body']);
    }

    public function testSiteUnknownReturnsNotFound(): void
    {
        $response = $this->request('/sites/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
    }

    public function testCountriesOverviewRenders(): void
    {
        $response = $this->request('/countries');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Countries', $response['body']);
        self::assertStringContainsString('Bahamas', $response['body']);
        self::assertStringContainsString('data-href="/countries/1"', $response['body']);
        self::assertStringContainsString('>3</td>', $response['body']);
    }

    public function testCountryDetailRenders(): void
    {
        $response = $this->request('/countries/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Bahamas', $response['body']);
        self::assertStringContainsString('Dives in this country', $response['body']);
        self::assertStringContainsString('data-href="/sites/10"', $response['body']);
        self::assertStringContainsString('data-href="/dives/2"', $response['body']);
    }

    public function testCountryUnknownReturnsNotFound(): void
    {
        $response = $this->request('/countries/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
    }

    public function testCitiesOverviewRenders(): void
    {
        $response = $this->request('/cities');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Cities', $response['body']);
        self::assertStringContainsString('Nassau', $response['body']);
    }

    public function testCityDetailRenders(): void
    {
        $response = $this->request('/cities/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Nassau', $response['body']);
    }

    public function testCityUnknownReturnsNotFound(): void
    {
        $response = $this->request('/cities/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
    }

    public function testShopsOverviewRenders(): void
    {
        $response = $this->request('/shops');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Shops', $response['body']);
        self::assertStringContainsString('Ocean Dive Center', $response['body']);
    }

    public function testShopDetailRenders(): void
    {
        $response = $this->request('/shops/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Ocean Dive Center', $response['body']);
    }

    public function testShopUnknownReturnsNotFound(): void
    {
        $response = $this->request('/shops/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
    }

    public function testTripsOverviewRenders(): void
    {
        $response = $this->request('/trips');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Trips', $response['body']);
        self::assertStringContainsString('Spring Bahamas', $response['body']);
        self::assertStringContainsString('data-href="/trips/1"', $response['body']);
        self::assertStringContainsString('>2</td>', $response['body']);
    }

    public function testTripDetailRenders(): void
    {
        $response = $this->request('/trips/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Spring Bahamas', $response['body']);
        self::assertStringContainsString('Dives in this trip', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
        self::assertStringContainsString('/countries/1', $response['body']);
    }

    public function testTripUnknownReturnsNotFound(): void
    {
        $response = $this->request('/trips/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
    }

    public function testEquipmentOverviewRenders(): void
    {
        $response = $this->request('/equipment');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Equipment', $response['body']);
        self::assertStringContainsString('Regulator', $response['body']);
        self::assertStringContainsString('data-href="/equipment/1"', $response['body']);
    }

    public function testEquipmentDetailRenders(): void
    {
        $response = $this->request('/equipment/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Regulator', $response['body']);
        self::assertStringContainsString('Dives using this equipment', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
    }

    public function testEquipmentUnknownReturnsNotFound(): void
    {
        $response = $this->request('/equipment/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
    }

    public function testUnmatchedRouteReturnsThemed404Page(): void
    {
        $response = $this->request('/this-page-definitely-does-not-exist');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Your Buddy Isn\'t Here', $response['body']);
        self::assertStringContainsString('Ascend to the Surface', $response['body']);
        self::assertStringContainsString('<meta name="robots" content="noindex,nofollow">', $response['body']);
    }

    public function testStatsOverviewRenders(): void
    {
        $response = $this->request('/stats');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Statistics', $response['body']);
        self::assertStringContainsString('Total dives', $response['body']);
        self::assertStringContainsString('Certifications', $response['body']);
        self::assertStringContainsString('Divemaster', $response['body']);
        self::assertStringContainsString('DM-491969', $response['body']);
        self::assertStringContainsString('cert-divemaster-front.jpg', $response['body']);
        self::assertStringContainsString('Depth distribution', $response['body']);
        self::assertStringContainsString('id="stats-depth-chart"', $response['body']);
        self::assertStringContainsString('data-depth-distribution=', $response['body']);
        self::assertStringContainsString('No-deco dives', $response['body']);
    }

    public function testGalleryRenders(): void
    {
        $response = $this->request('/gallery/100');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Gallery', $response['body']);
    }

    public function testGalleryOverviewRenders(): void
    {
        $response = $this->request('/gallery');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Log Gallery', $response['body']);
        self::assertStringContainsString('class="gallery-grid" data-lightbox-group="dive-log-gallery"', $response['body']);
        self::assertStringContainsString('data-dive-number="', $response['body']);
        self::assertStringContainsString('data-dive-url="/dives/', $response['body']);
        self::assertStringContainsString('/assets/js/lightbox.js', $response['body']);
        self::assertStringContainsString('<a href="/gallery" data-nav-link>', $response['body']);
    }

    public function testSummaryRenders(): void
    {
        $response = $this->request('/summary');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Summary', $response['body']);
    }

    public function testProfileDetailReturnsJson(): void
    {
        $response = $this->request('/profile/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('"diveNumber":1', $response['body']);
        self::assertStringContainsString('"depthUnit"', $response['body']);
        self::assertStringContainsString('"averageSeries"', $response['body']);
        self::assertStringContainsString('"ascentRateSeries"', $response['body']);
        self::assertStringContainsString('"descentRateSeries"', $response['body']);
    }

    public function testProfileUnknownReturnsJsonNotFound(): void
    {
        $response = $this->request('/profile/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('"code":"not_found"', $response['body']);
    }

    public function testDiveDetailSeoMetadataIsUniquePerDive(): void
    {
        $env = ['APP_URL' => 'https://dives.example.com'];

        $responseOne = $this->request('/dives/1', [], $env);
        $responseTwo = $this->request('/dives/2', [], $env);

        $titleOne = $this->extractTag('/<title>.*?<\/title>/s', $responseOne['body']);
        $titleTwo = $this->extractTag('/<title>.*?<\/title>/s', $responseTwo['body']);
        $descriptionOne = $this->extractTag('/<meta name="description"[^>]*>/', $responseOne['body']);
        $descriptionTwo = $this->extractTag('/<meta name="description"[^>]*>/', $responseTwo['body']);
        $canonicalOne = $this->extractTag('/<link rel="canonical"[^>]*>/', $responseOne['body']);
        $canonicalTwo = $this->extractTag('/<link rel="canonical"[^>]*>/', $responseTwo['body']);

        self::assertNotNull($titleOne);
        self::assertNotNull($descriptionOne);
        self::assertNotNull($canonicalOne);
        self::assertNotSame($titleOne, $titleTwo);
        self::assertNotSame($descriptionOne, $descriptionTwo);
        self::assertNotSame($canonicalOne, $canonicalTwo);
        self::assertStringContainsString('/dives/1', (string) $canonicalOne);
        self::assertStringContainsString('/dives/2', (string) $canonicalTwo);
    }

    public function testSiteDetailSeoMetadataIsUniquePerSite(): void
    {
        $env = ['APP_URL' => 'https://dives.example.com'];

        $responseOne = $this->request('/sites/10', [], $env);
        $responseTwo = $this->request('/sites/11', [], $env);

        $titleOne = $this->extractTag('/<title>.*?<\/title>/s', $responseOne['body']);
        $titleTwo = $this->extractTag('/<title>.*?<\/title>/s', $responseTwo['body']);
        $descriptionOne = $this->extractTag('/<meta name="description"[^>]*>/', $responseOne['body']);
        $descriptionTwo = $this->extractTag('/<meta name="description"[^>]*>/', $responseTwo['body']);
        $canonicalOne = $this->extractTag('/<link rel="canonical"[^>]*>/', $responseOne['body']);
        $canonicalTwo = $this->extractTag('/<link rel="canonical"[^>]*>/', $responseTwo['body']);

        self::assertNotNull($titleOne);
        self::assertNotNull($descriptionOne);
        self::assertNotNull($canonicalOne);
        self::assertNotSame($titleOne, $titleTwo);
        self::assertNotSame($descriptionOne, $descriptionTwo);
        self::assertNotSame($canonicalOne, $canonicalTwo);
    }

    public function testDiveDetailJsonLdIsValidWebPageMatchingCanonicalAndTitle(): void
    {
        $response = $this->request('/dives/1', [], ['APP_URL' => 'https://dives.example.com']);

        $canonical = $this->extractTag('/<link rel="canonical" href="([^"]*)"[^>]*>/', $response['body']);
        self::assertNotNull($canonical);
        preg_match('/href="([^"]*)"/', $canonical, $hrefMatch);
        $canonicalUrl = $hrefMatch[1] ?? null;

        preg_match('/<title>(.*?)<\/title>/s', $response['body'], $titleMatch);
        $titleText = $titleMatch[1] ?? null;

        $jsonLd = $this->extractJsonLd($response['body']);

        self::assertNotNull($jsonLd);
        self::assertSame('https://schema.org', $jsonLd['@context'] ?? null);
        self::assertSame('WebPage', $jsonLd['@type'] ?? null);
        self::assertSame($canonicalUrl, $jsonLd['url'] ?? null);
        self::assertSame($titleText, $jsonLd['name'] ?? null);
        self::assertArrayHasKey('description', $jsonLd);
    }

    public function testCanonicalUrlUsesQueryStringFormWhenQueryStringModeIsActive(): void
    {
        $response = $this->request('/dives/1', [], [
            'APP_URL' => 'https://dives.example.com',
            'APP_QUERY_STRING' => 'true',
        ]);

        $canonical = $this->extractTag('/<link rel="canonical"[^>]*>/', $response['body']);

        self::assertNotNull($canonical);
        self::assertStringContainsString('?type=dives&amp;id=1', $canonical);
    }

    public function testCanonicalUrlUsesPathFormWhenPathModeIsActive(): void
    {
        $response = $this->request('/dives/1', [], [
            'APP_URL' => 'https://dives.example.com',
            'APP_QUERY_STRING' => 'false',
        ]);

        $canonical = $this->extractTag('/<link rel="canonical"[^>]*>/', $response['body']);

        self::assertNotNull($canonical);
        self::assertStringContainsString('href="https://dives.example.com/dives/1"', $canonical);
    }

    public function testSeoOptOutEmitsNoindexAndOmitsCanonicalAndSchema(): void
    {
        $response = $this->request('/dives/1', [], [
            'APP_URL' => 'https://dives.example.com',
            'APP_SEO_ENABLED' => 'false',
        ]);

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('<meta name="robots" content="noindex,nofollow">', $response['body']);
        self::assertStringNotContainsString('rel="canonical"', $response['body']);
        self::assertStringNotContainsString('application/ld+json', $response['body']);
        self::assertStringNotContainsString('Dive #1', $this->extractTag('/<title>.*?<\/title>/s', $response['body']) ?? '');
    }

    /**
     * The embeddable summary fragment (divesummary.html.twig) intentionally has no <head> --
     * it's designed to be dropped into another page without imposing this app's chrome -- so
     * its noindex signal is sent via an X-Robots-Tag response header (public/index.php's
     * renderPage()), not a <meta> tag. PHP's plain `cli` SAPI never populates headers_list()
     * (confirmed: header_remove() + headers_list() stays empty even after header() calls,
     * regardless of SAPI settings), so the header itself can't be asserted through this
     * include()-based harness without a real HTTP server, which is out of scope here. What IS
     * verified here and by PageSeoContextBuilderTest (Support/Seo) together:
     *  - PageSeoContextBuilder::build() returns robots => 'noindex,nofollow' for
     *    'summary.overview' when SEO is enabled (testSummaryOverviewOnlySetsRobotsNoIndex).
     *  - It also returns 'noindex,nofollow' for ANY route -- including summary.overview --
     *    when the global opt-out is active, since that branch is checked first and doesn't
     *    inspect the route (testOptedOutForcesTitleAndDescriptionNullAndSetsNoindex).
     * Together those prove "regardless of the flag" for the computed value; this test proves
     * the route still renders correctly (no crash, same content) under both flag states, i.e.
     * that wiring PageSeoContextBuilder into this route didn't regress it.
     */
    public function testSummaryRendersConsistentlyRegardlessOfSeoFlag(): void
    {
        $enabledResponse = $this->request('/summary', [], ['APP_SEO_ENABLED' => 'true']);
        $disabledResponse = $this->request('/summary', [], ['APP_SEO_ENABLED' => 'false']);

        self::assertSame(200, $enabledResponse['status']);
        self::assertSame(200, $disabledResponse['status']);
        self::assertStringContainsString('Dive Summary', $enabledResponse['body']);
        self::assertStringContainsString('Dive Summary', $disabledResponse['body']);
        self::assertSame($enabledResponse['body'], $disabledResponse['body']);
    }

    public function testSitemapXmlListsAllPagesWhenBaseUrlConfigured(): void
    {
        $response = $this->request('/sitemap.xml', [], ['APP_URL' => 'https://dives.example.com']);

        self::assertSame(200, $response['status']);

        $document = new \DOMDocument();
        self::assertTrue($document->loadXML($response['body']));
        self::assertStringContainsString('<loc>https://dives.example.com/</loc>', $response['body']);
        self::assertStringContainsString('<loc>https://dives.example.com/dives/1</loc>', $response['body']);
        self::assertStringContainsString('<loc>https://dives.example.com/sites/10</loc>', $response['body']);
        self::assertStringContainsString('<loc>https://dives.example.com/gallery</loc>', $response['body']);
    }

    public function testSitemapXmlReturns404WhenBaseUrlNotConfigured(): void
    {
        // The default test environment doesn't set APP_URL.
        $response = $this->request('/sitemap.xml');

        self::assertSame(404, $response['status']);
    }

    public function testSitemapXmlReturns404WhenSeoDisabled(): void
    {
        $response = $this->request('/sitemap.xml', [], [
            'APP_URL' => 'https://dives.example.com',
            'APP_SEO_ENABLED' => 'false',
        ]);

        self::assertSame(404, $response['status']);
    }

    public function testRobotsTxtReferencesSitemapWhenSeoEnabled(): void
    {
        $response = $this->request('/robots.txt', [], ['APP_URL' => 'https://dives.example.com']);

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Allow: /', $response['body']);
        self::assertStringContainsString('Sitemap: https://dives.example.com/sitemap.xml', $response['body']);
    }

    public function testRobotsTxtDisallowsAllWhenSeoDisabled(): void
    {
        $response = $this->request('/robots.txt', [], [
            'APP_URL' => 'https://dives.example.com',
            'APP_SEO_ENABLED' => 'false',
        ]);

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Disallow: /', $response['body']);
        self::assertStringNotContainsString('Sitemap:', $response['body']);
    }

    private function seedFixtureDatabase(): void
    {
        $fixturesPath = dirname(__DIR__) . '/fixtures';
        $dbPath = $fixturesPath . '/http-smoke.sqlite';

        if (is_file($dbPath)) {
            unlink($dbPath);
        }

        $pdo = new \PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $schema = file_get_contents($fixturesPath . '/schema.sql');
        $seed = file_get_contents($fixturesPath . '/seed.sql');
        $certs = file_get_contents($fixturesPath . '/certs.sql');
        if ($schema === false || $seed === false || $certs === false) {
            self::fail('Could not load SQL fixtures.');
        }

        $pdo->exec($schema);
        $pdo->exec($seed);
        $pdo->exec($certs);
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, string> $env Overrides/additions to the default environment (e.g.
     *                                    APP_URL, APP_QUERY_STRING, APP_SEO_ENABLED). Applied on
     *                                    top of the defaults, so every call is fully deterministic
     *                                    regardless of previous calls in the same test process.
     * @return array{status:int, body:string}
     */
    private function request(string $uri, array $headers = [], array $env = []): array
    {
        http_response_code(200);

        $_SERVER = [
            'REQUEST_URI' => $uri,
            'REQUEST_METHOD' => 'GET',
        ];
        foreach ($headers as $name => $value) {
            $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
            $_SERVER[$serverKey] = $value;
        }
        $_GET = [];

        $defaults = [
            'DB_DSN' => 'sqlite:' . dirname(__DIR__) . '/fixtures/http-smoke.sqlite',
            'DB_USER' => 'test',
            'DB_PASSWORD' => '',
            'APP_QUERY_STRING' => 'false',
            'APP_ENV' => 'test',
            'APP_URL' => '',
            'APP_SEO_ENABLED' => 'true',
        ];

        foreach (array_merge($defaults, $env) as $name => $value) {
            putenv($name . '=' . $value);
        }

        ob_start();
        include dirname(__DIR__, 2) . '/public/index.php';
        $body = (string) ob_get_clean();

        return [
            'status' => http_response_code(),
            'body' => $body,
        ];
    }

    private function extractTag(string $pattern, string $body): ?string
    {
        return preg_match($pattern, $body, $matches) === 1 ? $matches[0] : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractJsonLd(string $body): ?array
    {
        if (preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $body, $matches) !== 1) {
            return null;
        }

        $decoded = json_decode($matches[1], true);

        return is_array($decoded) ? $decoded : null;
    }
}
