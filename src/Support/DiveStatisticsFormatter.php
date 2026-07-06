<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

final readonly class DiveStatisticsFormatter
{
    public function __construct(private UnitConverter $converter, private Formatter $formatter)
    {
    }

    public function percentageLabel(?int $count, int $total): string
    {
        $safeCount = max(0, $count ?? 0);
        if ($total <= 0) {
            return sprintf('%d (0%%)', $safeCount);
        }

        $percent = (int) round(($safeCount / $total) * 100);
        return sprintf('%d (%d%%)', $safeCount, $percent);
    }

    public function duration(?int $minutes): string
    {
        if ($minutes === null) {
            return '-';
        }

        return sprintf('%d min', $minutes);
    }

    public function bottomTime(?int $minutes): string
    {
        if ($minutes === null) {
            return '-';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remainingMinutes);
    }

    public function depth(?float $meters): string
    {
        if ($meters === null) {
            return '-';
        }

        return $this->formatter->formatDecimal($this->converter->depthToDisplay($meters), 2)
            . ' '
            . $this->converter->depthLabel();
    }

    public function temperature(?float $celsius): string
    {
        if ($celsius === null) {
            return '-';
        }

        return $this->formatter->formatDecimal($this->converter->temperatureToDisplay($celsius), 1)
            . '°'
            . $this->converter->temperatureLabel();
    }
}
