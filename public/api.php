<?php

declare(strict_types=1);

use PhpDivingLog\Adapters\Api\JsonResponse;
use PhpDivingLog\Adapters\Api\Router;
use PhpDivingLog\Adapters\Api\Controller\CityApiController;
use PhpDivingLog\Adapters\Api\Controller\CountryApiController;
use PhpDivingLog\Adapters\Api\Controller\DiveApiController;
use PhpDivingLog\Adapters\Api\Controller\DiveSiteApiController;
use PhpDivingLog\Adapters\Api\Controller\EquipmentApiController;
use PhpDivingLog\Adapters\Api\Controller\ShopApiController;
use PhpDivingLog\Adapters\Api\Controller\StatsApiController;
use PhpDivingLog\Adapters\Api\Controller\TripApiController;

$container = require dirname(__DIR__) . '/adapters/api/bootstrap.php';
$router = new Router();
$match = $router->resolve($_SERVER['REQUEST_URI'] ?? '/');

$repositories = $container['repositories'];

$diveApi = new DiveApiController($repositories['dives']);
$siteApi = new DiveSiteApiController($repositories['diveSites']);
$countryApi = new CountryApiController($repositories['countries']);
$cityApi = new CityApiController($repositories['cities']);
$shopApi = new ShopApiController($repositories['shops']);
$tripApi = new TripApiController($repositories['trips']);
$equipmentApi = new EquipmentApiController($repositories['equipment']);
$statsApi = new StatsApiController($repositories['stats']);

if ($match['resource'] === 'dives') {
    if ($match['id'] === null) {
        JsonResponse::send($diveApi->list());
        return;
    }

    $payload = $diveApi->item($match['id']);
    if ($payload === null) {
        JsonResponse::error('not_found', 'Dive not found', 404);
        return;
    }

    JsonResponse::send($payload);
    return;
}

if ($match['resource'] === 'stats') {
    JsonResponse::send($statsApi->view());
    return;
}

if ($match['resource'] === 'sites') {
    if ($match['id'] === null) {
        JsonResponse::send($siteApi->list());
        return;
    }

    $payload = $siteApi->item($match['id']);
    if ($payload === null) {
        JsonResponse::error('not_found', 'Site not found', 404);
        return;
    }

    JsonResponse::send($payload);
    return;
}

if ($match['resource'] === 'countries') {
    if ($match['id'] === null) {
        JsonResponse::send($countryApi->list());
        return;
    }

    $payload = $countryApi->item($match['id']);
    if ($payload === null) {
        JsonResponse::error('not_found', 'Country not found', 404);
        return;
    }

    JsonResponse::send($payload);
    return;
}

if ($match['resource'] === 'cities') {
    if ($match['id'] === null) {
        JsonResponse::send($cityApi->list());
        return;
    }

    $payload = $cityApi->item($match['id']);
    if ($payload === null) {
        JsonResponse::error('not_found', 'City not found', 404);
        return;
    }

    JsonResponse::send($payload);
    return;
}

if ($match['resource'] === 'shops') {
    if ($match['id'] === null) {
        JsonResponse::send($shopApi->list());
        return;
    }

    $payload = $shopApi->item($match['id']);
    if ($payload === null) {
        JsonResponse::error('not_found', 'Shop not found', 404);
        return;
    }

    JsonResponse::send($payload);
    return;
}

if ($match['resource'] === 'trips') {
    if ($match['id'] === null) {
        JsonResponse::send($tripApi->list());
        return;
    }

    $payload = $tripApi->item($match['id']);
    if ($payload === null) {
        JsonResponse::error('not_found', 'Trip not found', 404);
        return;
    }

    JsonResponse::send($payload);
    return;
}

if ($match['resource'] === 'equipment') {
    if ($match['id'] === null) {
        JsonResponse::send($equipmentApi->list());
        return;
    }

    $payload = $equipmentApi->item($match['id']);
    if ($payload === null) {
        JsonResponse::error('not_found', 'Equipment item not found', 404);
        return;
    }

    JsonResponse::send($payload);
    return;
}

JsonResponse::error('not_found', 'Resource not found', 404);
