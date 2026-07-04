<?php

declare(strict_types=1);

use PhpDivingLog\Database\Connection;
use PhpDivingLog\Repository\AppInfoRepository;
use PhpDivingLog\Repository\BuddyRepository;
use PhpDivingLog\Repository\CityRepository;
use PhpDivingLog\Repository\CountryRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Repository\EquipmentRepository;
use PhpDivingLog\Repository\PersonalRepository;
use PhpDivingLog\Repository\PictureRepository;
use PhpDivingLog\Repository\ShopRepository;
use PhpDivingLog\Repository\StatsRepository;
use PhpDivingLog\Repository\TankRepository;
use PhpDivingLog\Repository\TripRepository;
use PhpDivingLog\Repository\UserDefinedRepository;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\DiveMetricsCalculator;
use PhpDivingLog\Support\HtmlSanitizer;
use PhpDivingLog\Support\MediaResolver;
use PhpDivingLog\Support\RtfConverter;
use PhpDivingLog\Support\ThumbnailGenerator;
use PhpDivingLog\Support\Translator;
use PhpDivingLog\Support\UnitConverter;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$envFile = dirname(__DIR__, 2) . '/.env';
$config = Config::fromEnvironment(is_file($envFile) ? $envFile : null);
$pdo = Connection::fromConfig($config);
$prefix = Connection::validatedTablePrefix($config->tablePrefix());

return [
    'config' => $config,
    'pdo' => $pdo,
    'services' => [
        'unitConverter' => new UnitConverter($config),
        'formatter' => new Formatter($config),
        'diveMetrics' => new DiveMetricsCalculator(new UnitConverter($config), new Formatter($config)),
        'translator' => Translator::fromFiles($config->language(), dirname(__DIR__, 2) . '/resources/lang'),
        'mediaResolver' => new MediaResolver(
            $config,
            new ThumbnailGenerator(
                dirname(__DIR__, 2) . '/public',
                $config->thumbWidth(),
                $config->thumbHeight(),
            ),
        ),
        'rtfConverter' => new RtfConverter(new HtmlSanitizer()),
    ],
    'repositories' => [
        'dives' => new DiveRepository($pdo, $prefix),
        'diveSites' => new DiveSiteRepository($pdo, $prefix),
        'countries' => new CountryRepository($pdo, $prefix),
        'cities' => new CityRepository($pdo, $prefix),
        'shops' => new ShopRepository($pdo, $prefix),
        'trips' => new TripRepository($pdo, $prefix),
        'equipment' => new EquipmentRepository($pdo, $prefix),
        'stats' => new StatsRepository($pdo, $prefix),
        'buddies' => new BuddyRepository($pdo, $prefix),
        'pictures' => new PictureRepository($pdo, $prefix),
        'tanks' => new TankRepository($pdo, $prefix),
        'userDefined' => new UserDefinedRepository($pdo, $prefix),
        'personal' => new PersonalRepository($pdo, $prefix),
        'appInfo' => new AppInfoRepository($pdo, $prefix, $config),
    ],
];
