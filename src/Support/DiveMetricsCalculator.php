<?php

declare(strict_types=1);

namespace PhpDivingLog\Support;

use PhpDivingLog\Model\Dive;
use PhpDivingLog\Model\Tank;

final readonly class DiveMetricsCalculator
{
    public function __construct(private UnitConverter $converter, private Formatter $formatter)
    {
    }

    /**
     * @param list<Tank> $tanks
     * @return array{averageDepthDisplay:string,sacDisplay:string}
     */
    public function calculate(Dive $dive, array $tanks): array
    {
        $averageDepthMeters = $this->averageDepthMeters($dive);
        if ($averageDepthMeters === null) {
            return [
                'averageDepthDisplay' => '-',
                'sacDisplay' => '-',
            ];
        }

        $averageDepthDisplay = $this->formatter->formatDecimal($this->converter->depthToDisplay($averageDepthMeters), 2)
            . ' '
            . $this->converter->depthLabel();

        $sacLitresPerMinute = $this->sacLitresPerMinute($dive, $averageDepthMeters, $tanks);
        if ($sacLitresPerMinute === null) {
            return [
                'averageDepthDisplay' => $averageDepthDisplay,
                'sacDisplay' => '-',
            ];
        }

        $sacDisplay = $this->formatter->formatDecimal($this->converter->volumeToDisplay($sacLitresPerMinute), 2)
            . ' '
            . $this->converter->volumeLabel()
            . '/min';

        return [
            'averageDepthDisplay' => $averageDepthDisplay,
            'sacDisplay' => $sacDisplay,
        ];
    }

    private function averageDepthMeters(Dive $dive): ?float
    {
        $profile = $dive->extra['profile'] ?? null;
        if (!is_string($profile) || $profile === '') {
            return null;
        }

        $length = intdiv(strlen($profile), 12);
        if ($length <= 0) {
            return null;
        }

        $sum = 0.0;
        $offset = 0;
        for ($i = 0; $i < $length; $i++) {
            $slice = substr($profile, $offset, 12);
            if (strlen($slice) < 5) {
                break;
            }

            $sum += ((float) substr($slice, 0, 5)) / 100.0;
            $offset += 12;
        }

        if ($offset === 0) {
            return null;
        }

        $sampleCount = intdiv($offset, 12);
        return $sampleCount > 0 ? $sum / $sampleCount : null;
    }

    /**
     * @param list<Tank> $tanks
     */
    private function sacLitresPerMinute(Dive $dive, float $averageDepthMeters, array $tanks): ?float
    {
        if ($dive->durationMinutes <= 0) {
            return null;
        }

        $surfacePressureFactor = ($averageDepthMeters / 10.0) + 1.0;
        if ($surfacePressureFactor <= 0) {
            return null;
        }

        $totalLitresAtSurface = 0.0;
        foreach ($tanks as $tank) {
            if (
                $tank->volume === null
                || $tank->pressureStart === null
                || $tank->pressureEnd === null
                || $tank->pressureStart <= $tank->pressureEnd
            ) {
                continue;
            }

            $totalLitresAtSurface += ($tank->pressureStart - $tank->pressureEnd) * $tank->volume;
        }

        if ($totalLitresAtSurface <= 0) {
            return null;
        }

        return $totalLitresAtSurface / ($dive->durationMinutes * $surfacePressureFactor);
    }
}
