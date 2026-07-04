<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

use DateTimeInterface;

final readonly class Formatter
{
    public function __construct(private Config $config)
    {
    }

    public function formatDate(DateTimeInterface $dateTime): string
    {
        $parts = explode(':', $this->config->dateFormat());
        if (count($parts) !== 3 || $parts[0] !== 'date') {
            return $dateTime->format('Y-m-d');
        }

        $order = $parts[1];
        $sep = $parts[2];

        return match ($order) {
            'mdy' => $dateTime->format('m' . $sep . 'd' . $sep . 'Y'),
            'ymd' => $dateTime->format('Y' . $sep . 'm' . $sep . 'd'),
            default => $dateTime->format('d' . $sep . 'm' . $sep . 'Y'),
        };
    }

    public function formatCoordinate(float $decimalDegree): string
    {
        $mode = $this->config->coordFormat();
        $abs = abs($decimalDegree);

        if ($mode === 'd') {
            return sprintf('%.6f', $decimalDegree);
        }

        $deg = floor($abs);
        $minutesFull = ($abs - $deg) * 60.0;

        if ($mode === 'dm') {
            $prefix = $decimalDegree < 0 ? '-' : '';
            return sprintf('%s%d %.4f', $prefix, (int) $deg, $minutesFull);
        }

        $minutes = floor($minutesFull);
        $seconds = ($minutesFull - $minutes) * 60.0;
        $prefix = $decimalDegree < 0 ? '-' : '';

        return sprintf('%s%d %d %.2f', $prefix, (int) $deg, (int) $minutes, $seconds);
    }

    public function formatDecimal(float $value, int $precision = 2): string
    {
        return number_format($value, $precision, $this->config->decimalSeparator(), '');
    }
}
