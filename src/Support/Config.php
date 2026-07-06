<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final class Config
{
    /**
     * @param array<string, mixed> $values
     */
    private function __construct(private readonly array $values)
    {
    }

    public static function fromEnvironment(?string $envFile = null): self
    {
        $fileValues = self::loadEnvFile($envFile);
        $raw = [];

        foreach (array_keys(self::defaults()) as $name) {
            $env = getenv($name);
            if ($env !== false) {
                $raw[$name] = $env;
                continue;
            }

            if (array_key_exists($name, $fileValues)) {
                $raw[$name] = $fileValues[$name];
            }
        }

        return self::fromArray($raw);
    }

    /**
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        $values = self::defaults();
        foreach ($raw as $key => $value) {
            if (array_key_exists($key, $values) && $value !== null) {
                $values[$key] = trim((string) $value);
            }
        }

        self::validateRequiredDatabase($values);

        return new self([
            'app_name' => self::asString($values, 'APP_NAME'),
            'app_version' => self::asString($values, 'APP_VERSION'),
            'app_url' => self::asString($values, 'APP_URL'),
            'dlog_url' => self::asString($values, 'DLOG_URL'),
            'dlog_version' => self::asString($values, 'DLOG_VERSION'),

            'database_dsn' => self::databaseDsn($values),
            'database_host' => self::asString($values, 'DB_HOST'),
            'database_port' => self::asInt($values, 'DB_PORT'),
            'database_name' => self::asString($values, 'DB_NAME'),
            'database_username' => self::asString($values, 'DB_USER'),
            'database_password' => self::asString($values, 'DB_PASSWORD'),
            'table_prefix' => self::asString($values, 'TABLE_PREFIX'),

            'app_env' => self::asString($values, 'APP_ENV'),
            'app_debug' => self::asBool($values, 'APP_DEBUG'),
            'language' => self::asString($values, 'APP_LANGUAGE'),
            'query_string' => self::asBool($values, 'APP_QUERY_STRING'),
            'embed_mode' => self::asBool($values, 'APP_EMBED_MODE'),

            'max_list' => self::asInt($values, 'APP_MAX_LIST'),
            'show_profile' => self::asBool($values, 'APP_SHOW_PROFILE'),
            'dlog_comments_rtf' => self::asBool($values, 'APP_DLOG_COMMENTS_RTF'),
            'user_show' => self::asBool($values, 'APP_USER_SHOW'),
            'user_show_general' => self::asBool($values, 'APP_USER_SHOW_GENERAL'),
            'user_show_comments' => self::asBool($values, 'APP_USER_SHOW_COMMENTS'),
            'user_show_photo' => self::asBool($values, 'APP_USER_SHOW_PHOTO'),
            'user_show_certs' => self::asBool($values, 'APP_USER_SHOW_CERTS'),
            'user_show_medical' => self::asBool($values, 'APP_USER_SHOW_MEDICAL'),
            'equipment_service_reminder' => self::asBool($values, 'APP_EQUIPMENT_SERVICE_REMINDER'),
            'equipment_service_warning' => self::asInt($values, 'APP_EQUIPMENT_SERVICE_WARNING'),
            'comma_separated' => self::asBool($values, 'APP_COMMA_SEPARATED'),
            'comma_separator' => self::asString($values, 'APP_COMMA_SEPARATOR'),

            'thumb_width' => self::asInt($values, 'APP_THUMB_WIDTH'),
            'thumb_height' => self::asInt($values, 'APP_THUMB_HEIGHT'),
            'pic_width' => self::asInt($values, 'APP_PIC_WIDTH'),
            'get_exif_data' => self::asBool($values, 'APP_GET_EXIF_DATA'),
            'graph_background_image' => self::asString($values, 'APP_GRAPH_BACKGROUND_IMAGE'),
            'graph_show_two_scales' => self::asBool($values, 'APP_GRAPH_SHOW_TWO_SCALES'),
            'graph_show_both_units' => self::asBool($values, 'APP_GRAPH_SHOW_BOTH_UNITS'),
            'background_color' => self::asString($values, 'APP_BACKGROUND_COLOR'),

            'date_format' => self::asString($values, 'APP_DATE_FORMAT'),
            'coord_format' => self::asString($values, 'APP_COORD_FORMAT'),
            'decsep' => self::asString($values, 'APP_DECIMAL_SEPARATOR'),
            'length' => self::asBool($values, 'APP_CONVERT_LENGTH'),
            'pressure' => self::asBool($values, 'APP_CONVERT_PRESSURE'),
            'weight' => self::asBool($values, 'APP_CONVERT_WEIGHT'),
            'temp' => self::asBool($values, 'APP_CONVERT_TEMP'),
            'volume' => self::asBool($values, 'APP_CONVERT_VOLUME'),

            'default_o2' => self::asFloat($values, 'APP_DEFAULT_O2'),
            'default_maxppo2' => self::asFloat($values, 'APP_DEFAULT_MAX_PPO2'),

            'picpath_web' => self::asString($values, 'APP_PIC_PATH_WEB'),
            'picpath_web_thumb' => self::asString($values, 'APP_PIC_PATH_WEB_THUMB'),
            'pic_missing' => self::asString($values, 'APP_PIC_MISSING'),
            'mappath_web' => self::asString($values, 'APP_MAP_PATH_WEB'),
            'equippath_web' => self::asString($values, 'APP_EQUIP_PATH_WEB'),
            'flagpath_web' => self::asString($values, 'APP_FLAG_PATH_WEB'),
            'userpath_web' => self::asString($values, 'APP_USER_PATH_WEB'),
            'buddypath_web' => self::asString($values, 'APP_BUDDY_PATH_WEB'),
            'shoppath_web' => self::asString($values, 'APP_SHOP_PATH_WEB'),
            'trippath_web' => self::asString($values, 'APP_TRIP_PATH_WEB'),

            'web_root' => self::asString($values, 'APP_WEB_ROOT'),
            'abs_url_path' => self::asString($values, 'APP_ABS_URL_PATH'),
        ]);
    }

    public function appName(): string
    {
        return $this->values['app_name'];
    }

    public function userShowCerts(): bool
    {
        return $this->values['user_show_certs'];
    }

    public function userShowPhoto(): bool
    {
        return $this->values['user_show_photo'];
    }

    public function appVersion(): string
    {
        return $this->values['app_version'];
    }

    public function appUrl(): string
    {
        return $this->values['app_url'];
    }

    public function appEnvironment(): string
    {
        return $this->values['app_env'];
    }

    public function appDebug(): bool
    {
        return $this->values['app_debug'];
    }

    public function language(): string
    {
        return $this->values['language'];
    }

    public function queryStringMode(): bool
    {
        return $this->values['query_string'];
    }

    public function dsn(): string
    {
        return $this->values['database_dsn'];
    }

    public function databaseUser(): string
    {
        return $this->values['database_username'];
    }

    public function databasePassword(): string
    {
        return $this->values['database_password'];
    }

    public function databaseName(): string
    {
        return $this->values['database_name'];
    }

    public function tablePrefix(): string
    {
        return $this->values['table_prefix'];
    }

    public function maxList(): int
    {
        return $this->values['max_list'];
    }

    public function showProfile(): bool
    {
        return $this->values['show_profile'];
    }

    public function dateFormat(): string
    {
        return $this->values['date_format'];
    }

    public function coordFormat(): string
    {
        return $this->values['coord_format'];
    }

    public function decimalSeparator(): string
    {
        return $this->values['decsep'];
    }

    public function convertLength(): bool
    {
        return $this->values['length'];
    }

    public function convertPressure(): bool
    {
        return $this->values['pressure'];
    }

    public function convertWeight(): bool
    {
        return $this->values['weight'];
    }

    public function convertTemperature(): bool
    {
        return $this->values['temp'];
    }

    public function convertVolume(): bool
    {
        return $this->values['volume'];
    }

    public function thumbWidth(): int
    {
        return $this->values['thumb_width'];
    }

    public function thumbHeight(): int
    {
        return $this->values['thumb_height'];
    }

    public function picPathWeb(): string
    {
        return $this->values['picpath_web'];
    }

    public function picPathWebThumb(): string
    {
        return $this->values['picpath_web_thumb'];
    }

    public function picMissing(): string
    {
        return $this->values['pic_missing'];
    }

    public function mapPathWeb(): string
    {
        return $this->values['mappath_web'];
    }

    public function equipPathWeb(): string
    {
        return $this->values['equippath_web'];
    }

    public function flagPathWeb(): string
    {
        return $this->values['flagpath_web'];
    }

    public function userPathWeb(): string
    {
        return $this->values['userpath_web'];
    }

    public function buddyPathWeb(): string
    {
        return $this->values['buddypath_web'];
    }

    public function shopPathWeb(): string
    {
        return $this->values['shoppath_web'];
    }

    public function tripPathWeb(): string
    {
        return $this->values['trippath_web'];
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values;
    }

    /**
     * @return array<string, string>
     */
    private static function defaults(): array
    {
        return [
            'APP_NAME' => 'phpDivingLog',
            'APP_VERSION' => '3.2',
            'APP_URL' => 'https://github.com/Infern1/phpDivinglog',
            'DLOG_URL' => 'http://www.divinglog.de/',
            'DLOG_VERSION' => '6.0.22',

            'DB_DSN' => '',
            'DB_HOST' => 'localhost',
            'DB_PORT' => '3306',
            'DB_NAME' => '',
            'DB_USER' => '',
            'DB_PASSWORD' => '',
            'TABLE_PREFIX' => 'DL_',

            'APP_ENV' => 'prod',
            'APP_DEBUG' => 'false',
            'APP_LANGUAGE' => 'english',
            'APP_QUERY_STRING' => 'true',
            'APP_EMBED_MODE' => 'false',
            'APP_MAX_LIST' => '20',
            'APP_SHOW_PROFILE' => 'true',
            'APP_DLOG_COMMENTS_RTF' => 'true',
            'APP_USER_SHOW' => 'true',
            'APP_USER_SHOW_GENERAL' => 'true',
            'APP_USER_SHOW_COMMENTS' => 'true',
            'APP_USER_SHOW_PHOTO' => 'true',
            'APP_USER_SHOW_CERTS' => 'true',
            'APP_USER_SHOW_MEDICAL' => 'true',
            'APP_EQUIPMENT_SERVICE_REMINDER' => 'true',
            'APP_EQUIPMENT_SERVICE_WARNING' => '30',
            'APP_COMMA_SEPARATED' => 'true',
            'APP_COMMA_SEPARATOR' => ' |',

            'APP_THUMB_WIDTH' => '100',
            'APP_THUMB_HEIGHT' => '75',
            'APP_PIC_WIDTH' => '800',
            'APP_GET_EXIF_DATA' => 'true',
            'APP_GRAPH_BACKGROUND_IMAGE' => '',
            'APP_GRAPH_SHOW_TWO_SCALES' => 'true',
            'APP_GRAPH_SHOW_BOTH_UNITS' => 'true',
            'APP_BACKGROUND_COLOR' => '#ffffff',

            'APP_DATE_FORMAT' => 'date:dmy:-',
            'APP_COORD_FORMAT' => 'dm',
            'APP_DECIMAL_SEPARATOR' => ',',
            'APP_CONVERT_LENGTH' => 'false',
            'APP_CONVERT_PRESSURE' => 'false',
            'APP_CONVERT_WEIGHT' => 'false',
            'APP_CONVERT_TEMP' => 'false',
            'APP_CONVERT_VOLUME' => 'false',

            'APP_DEFAULT_O2' => '21',
            'APP_DEFAULT_MAX_PPO2' => '1.4',

            'APP_PIC_PATH_WEB' => '/images/pictures/',
            'APP_PIC_PATH_WEB_THUMB' => '/images/pictures/thumb',
            'APP_PIC_MISSING' => '/images/icons8-no-image-50.png',
            'APP_MAP_PATH_WEB' => '/images/maps/',
            'APP_EQUIP_PATH_WEB' => '/images/equipment/',
            'APP_FLAG_PATH_WEB' => '/images/flags/',
            'APP_USER_PATH_WEB' => '/images/userinfo/',
            'APP_BUDDY_PATH_WEB' => '/images/buddies/',
            'APP_SHOP_PATH_WEB' => '/images/shops/',
            'APP_TRIP_PATH_WEB' => '/images/trips/',

            'APP_WEB_ROOT' => '',
            'APP_ABS_URL_PATH' => '',
        ];
    }

    /**
     * @param array<string, string> $values
     */
    private static function validateRequiredDatabase(array $values): void
    {
        $hasDsn = trim($values['DB_DSN']) !== '';
        $hasHost = trim($values['DB_HOST']) !== '';
        $hasName = trim($values['DB_NAME']) !== '';
        $hasUser = trim($values['DB_USER']) !== '';

        if ($hasDsn && $hasUser) {
            return;
        }

        if ($hasHost && $hasName && $hasUser) {
            return;
        }

        throw new ConfigException('Missing required database configuration. Set DB_DSN and DB_USER, or set DB_HOST, DB_NAME, and DB_USER.');
    }

    /**
     * @param array<string, string> $values
     */
    private static function databaseDsn(array $values): string
    {
        $dsn = trim($values['DB_DSN']);
        if ($dsn !== '') {
            return $dsn;
        }

        $host = self::asString($values, 'DB_HOST');
        $port = self::asInt($values, 'DB_PORT');
        $name = self::asString($values, 'DB_NAME');

        return sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, $port, $name);
    }

    /**
     * @param array<string, mixed> $values
     */
    private static function asString(array $values, string $key): string
    {
        return trim((string) ($values[$key] ?? ''));
    }

    /**
     * @param array<string, mixed> $values
     */
    private static function asInt(array $values, string $key): int
    {
        $value = (string) ($values[$key] ?? '');
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new ConfigException(sprintf('Invalid integer value for %s.', $key));
        }

        return (int) $value;
    }

    /**
     * @param array<string, mixed> $values
     */
    private static function asFloat(array $values, string $key): float
    {
        $value = (string) ($values[$key] ?? '');
        if (!is_numeric($value)) {
            throw new ConfigException(sprintf('Invalid float value for %s.', $key));
        }

        return (float) $value;
    }

    /**
     * @param array<string, mixed> $values
     */
    private static function asBool(array $values, string $key): bool
    {
        $value = filter_var((string) ($values[$key] ?? ''), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($value === null) {
            throw new ConfigException(sprintf('Invalid boolean value for %s.', $key));
        }

        return $value;
    }

    /**
     * @return array<string, string>
     */
    private static function loadEnvFile(?string $path): array
    {
        if ($path === null || $path === '' || !is_file($path)) {
            return [];
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new ConfigException('Unable to read environment file.');
        }

        $values = [];
        $lines = preg_split('/\r\n|\r|\n/', $content);
        if ($lines === false) {
            return [];
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            $pos = strpos($trimmed, '=');
            if ($pos === false) {
                continue;
            }

            $name = trim(substr($trimmed, 0, $pos));
            $value = trim(substr($trimmed, $pos + 1));
            if ($name === '') {
                continue;
            }

            $values[$name] = self::stripWrappingQuotes($value);
        }

        return $values;
    }

    private static function stripWrappingQuotes(string $value): string
    {
        if (
            strlen($value) >= 2
            && (($value[0] === '"' && $value[strlen($value) - 1] === '"') || ($value[0] === '\'' && $value[strlen($value) - 1] === '\''))
        ) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
