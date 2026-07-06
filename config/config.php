<?php

declare(strict_types=1);

use PhpDivingLog\Support\Config;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$envPath = dirname(__DIR__) . '/.env';

return Config::fromEnvironment(is_file($envPath) ? $envPath : null);
