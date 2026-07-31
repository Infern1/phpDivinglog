<?php

declare(strict_types=1);

use PhpDivingLog\Adapters\Web\Router;
use PhpDivingLog\Adapters\Web\TwigRenderer;
use PhpDivingLog\Adapters\Web\Controller\CityController;
use PhpDivingLog\Adapters\Web\Controller\CountryController;
use PhpDivingLog\Adapters\Web\Controller\DiveController;
use PhpDivingLog\Adapters\Web\Controller\DiveSiteController;
use PhpDivingLog\Adapters\Web\Controller\DiveStatisticsController;
use PhpDivingLog\Adapters\Web\Controller\EquipmentController;
use PhpDivingLog\Adapters\Web\Controller\GalleryController;
use PhpDivingLog\Adapters\Web\Controller\ProfileController;
use PhpDivingLog\Adapters\Web\Controller\ShopController;
use PhpDivingLog\Adapters\Web\Controller\SummaryController;
use PhpDivingLog\Adapters\Web\Controller\TripController;

$container = require dirname(__DIR__) . '/adapters/web/bootstrap.php';

$router = new Router();
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$queryMode = $container['config']->queryStringMode();

if ($queryMode) {
    if (isset($_GET['type'])) {
        $type = (string) $_GET['type'];
        $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int) $_GET['id'] : null;
        $match = [
            'route' => $id === null ? $type . '.overview' : $type . '.detail',
            'id' => $id,
        ];
    } else {
        $match = $router->resolve($requestUri);
    }
} else {
    $match = $router->resolve($requestUri);
}

$repositories = $container['repositories'];
$services = $container['services'];
$profile = $repositories['personal']->getProfile();
$displayName = trim(($profile?->firstName ?? '') . ' ' . ($profile?->lastName ?? ''));
$appName = $displayName !== '' ? $displayName . ' Dive Log' : 'phpDivingLog';

$renderer = new TwigRenderer(
    dirname(__DIR__) . '/templates',
    dirname(__DIR__) . '/var/cache/twig',
    ['app_name' => $appName]
);

$seoContextBuilder = $services['pageSeoContextBuilder'];

/**
 * @param array<string, mixed> $payload
 */
$renderPage = static function (string $template, array $payload, string $route, ?int $id) use ($renderer, $seoContextBuilder): string {
    $overrides = $seoContextBuilder->build($route, $id, $_GET, $payload['title'] ?? null, $payload['meta_description'] ?? null);

    if (isset($overrides['robots']) && $overrides['robots'] !== '') {
        // Sent as an HTTP header too (not just the <meta> tag) since some templates -- the
        // embeddable summary fragment, the AJAX dive-detail partial -- have no <head> at all.
        header('X-Robots-Tag: ' . $overrides['robots']);
    }

    return $renderer->render($template, array_merge($payload, $overrides));
};

$diveController = new DiveController(
    $repositories['dives'],
    $repositories['buddies'],
    $repositories['pictures'],
    $repositories['diveSites'],
    $repositories['countries'],
    $repositories['cities'],
    $repositories['shops'],
    $repositories['trips'],
    $repositories['tanks'],
    $repositories['userDefined'],
    $services['unitConverter'],
    $services['formatter'],
    $services['diveMetrics'],
    $services['rtfConverter'],
    $services['mediaResolver'],
    $services['descriptionTruncator']
);

$profileController = new ProfileController($repositories['dives'], $services['unitConverter']);
$siteController = new DiveSiteController($repositories['diveSites'], $repositories['dives'], $repositories['pictures'], $services['formatter'], $services['unitConverter'], $services['mediaResolver'], $services['descriptionTruncator']);
$countryController = new CountryController($repositories['countries'], $repositories['dives'], $repositories['diveSites'], $services['mediaResolver'], $services['formatter'], $services['unitConverter'], $services['descriptionTruncator']);
$cityController = new CityController($repositories['cities'], $services['descriptionTruncator']);
$shopController = new ShopController($repositories['shops'], $services['descriptionTruncator']);
$tripController = new TripController($repositories['trips'], $repositories['dives'], $services['formatter'], $services['unitConverter'], $services['descriptionTruncator']);
$equipmentController = new EquipmentController($repositories['equipment'], $repositories['dives'], $container['config'], $services['formatter'], $services['unitConverter'], $services['mediaResolver'], $services['descriptionTruncator']);
$statsController = new DiveStatisticsController($repositories['diveStatistics'], $repositories['certifications'], $services['diveStatisticsFormatter'], $services['formatter'], $services['unitConverter'], $services['mediaResolver'], $container['config'], $services['descriptionTruncator']);
$galleryController = new GalleryController(
    $repositories['pictures'],
    $services['mediaResolver'],
    $repositories['dives'],
    $repositories['personal'],
    $services['formatter'],
    $services['descriptionTruncator']
);
$summaryController = new SummaryController($repositories['stats']);

if ($match['route'] === 'profile.detail' && $match['id'] !== null) {
    $profile = $profileController->series($match['id']);
    if ($profile === null) {
        http_response_code(404);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => ['code' => 'not_found', 'message' => 'Dive not found']]);
        return;
    }

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($profile);
    return;
}

if ($match['route'] === 'dives.overview') {
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $search = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
    $sort = isset($_GET['sort']) ? (string) $_GET['sort'] : 'newest';
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('dives_overview.html.twig', $diveController->overview($page, 20, $search, $sort), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'dives.detail' && $match['id'] !== null) {
    $payload = $diveController->detail($match['id']);
    if ($payload === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Dive not found';
        return;
    }

    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    $isPartial = strtolower((string) $requestedWith) === 'xmlhttprequest'
        || (isset($_GET['partial']) && (string) $_GET['partial'] !== '' && (string) $_GET['partial'] !== '0');

    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage($isPartial ? 'dive_detail_partial.html.twig' : 'dive_detail.html.twig', $payload, $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'sites.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divesite_overview.html.twig', $siteController->overview(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'sites.detail' && $match['id'] !== null) {
    $payload = $siteController->detail($match['id']);
    if ($payload === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Site not found';
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divesite_detail.html.twig', $payload, $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'countries.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divecountry_overview.html.twig', $countryController->overview(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'countries.detail' && $match['id'] !== null) {
    $payload = $countryController->detail($match['id']);
    if ($payload === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Country not found';
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divecountry_detail.html.twig', $payload, $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'cities.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divecity_overview.html.twig', $cityController->overview(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'cities.detail' && $match['id'] !== null) {
    $payload = $cityController->detail($match['id']);
    if ($payload === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'City not found';
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divecity_detail.html.twig', $payload, $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'shops.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('diveshop_overview.html.twig', $shopController->overview(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'shops.detail' && $match['id'] !== null) {
    $payload = $shopController->detail($match['id']);
    if ($payload === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Shop not found';
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('diveshop_detail.html.twig', $payload, $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'trips.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divetrip_overview.html.twig', $tripController->overview(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'trips.detail' && $match['id'] !== null) {
    $payload = $tripController->detail($match['id']);
    if ($payload === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Trip not found';
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divetrip_detail.html.twig', $payload, $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'equipment.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('equipment_overview.html.twig', $equipmentController->overview(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'equipment.detail' && $match['id'] !== null) {
    $payload = $equipmentController->detail($match['id']);
    if ($payload === null) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Equipment not found';
        return;
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('equipment_detail.html.twig', $payload, $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'stats.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divestats.html.twig', $statsController->view(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'gallery.overview') {
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('dive_log_gallery.html.twig', $galleryController->overview($page, 24), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'gallery.detail' && $match['id'] !== null) {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divegallery.html.twig', $galleryController->forDive($match['id']), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'summary.overview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo $renderPage('divesummary.html.twig', $summaryController->embeddable(), $match['route'], $match['id']);
    return;
}

if ($match['route'] === 'not-found' || $match['route'] === 'profile.overview') {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Not found';
    return;
}

http_response_code(404);
header('Content-Type: text/plain; charset=UTF-8');
echo 'Not found';
