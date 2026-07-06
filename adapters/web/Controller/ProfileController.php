<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Support\UnitConverter;

final readonly class ProfileController
{
    public function __construct(private DiveRepository $dives, private UnitConverter $converter)
    {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function series(int $number): ?array
    {
        $dive = $this->dives->findByNumber($number);
        if ($dive === null) {
            return null;
        }

        $series = $this->buildDepthSeries($dive);
        $averageDepth = $this->averageDepth($series);
        $averageSeries = array_map(
            static fn (array $point): array => ['minute' => $point['minute'], 'depth' => $averageDepth],
            $series
        );

        [$ascentRateSeries, $descentRateSeries] = $this->rateSeries($series);

        return [
            'diveNumber' => $dive->number,
            'depthUnit' => $this->converter->depthLabel(),
            'rateUnit' => $this->converter->depthLabel() . '/min',
            'series' => $series,
            'averageSeries' => $averageSeries,
            'ascentRateSeries' => $ascentRateSeries,
            'descentRateSeries' => $descentRateSeries,
        ];
    }

    /**
     * @return list<array{minute:float,depth:float}>
     */
    private function buildDepthSeries(object $dive): array
    {
        $profile = $dive->extra['profile'] ?? null;
        $intervalSeconds = $dive->extra['profile_interval_seconds'] ?? null;

        if (!is_string($profile) || $profile === '' || !is_int($intervalSeconds) || $intervalSeconds <= 0) {
            return [
                ['minute' => 0.0, 'depth' => 0.0],
                [
                    'minute' => (float) max(1, intdiv($dive->durationMinutes, 2)),
                    'depth' => round($this->converter->depthToDisplay($dive->depthMax), 2),
                ],
                ['minute' => (float) $dive->durationMinutes, 'depth' => 0.0],
            ];
        }

        $samples = intdiv(strlen($profile), 12);
        if ($samples <= 0) {
            return [
                ['minute' => 0.0, 'depth' => 0.0],
                ['minute' => (float) $dive->durationMinutes, 'depth' => 0.0],
            ];
        }

        $series = [];
        $minute = 0.0;
        $intervalMinutes = $intervalSeconds / 60.0;
        for ($i = 0; $i < $samples; $i++) {
            $chunk = substr($profile, $i * 12, 12);
            if (strlen($chunk) < 5) {
                break;
            }

            $depthMeters = ((float) substr($chunk, 0, 5)) / 100.0;
            $series[] = [
                'minute' => round($minute, 2),
                'depth' => round($this->converter->depthToDisplay($depthMeters), 2),
            ];
            $minute += $intervalMinutes;
        }

        if ($series === []) {
            return [
                ['minute' => 0.0, 'depth' => 0.0],
                ['minute' => (float) $dive->durationMinutes, 'depth' => 0.0],
            ];
        }

        return $series;
    }

    /**
     * @param list<array{minute:float,depth:float}> $series
     */
    private function averageDepth(array $series): float
    {
        if ($series === []) {
            return 0.0;
        }

        $sum = array_sum(array_map(static fn (array $point): float => $point['depth'], $series));
        return round($sum / count($series), 2);
    }

    /**
     * @param list<array{minute:float,depth:float}> $series
     * @return array{0:list<array{minute:float,rate:float}>,1:list<array{minute:float,rate:float}>}
     */
    private function rateSeries(array $series): array
    {
        if (count($series) < 2) {
            return [[['minute' => 0.0, 'rate' => 0.0]], [['minute' => 0.0, 'rate' => 0.0]]];
        }

        $ascent = [];
        $descent = [];

        $ascent[] = ['minute' => $series[0]['minute'], 'rate' => 0.0];
        $descent[] = ['minute' => $series[0]['minute'], 'rate' => 0.0];

        for ($i = 1; $i < count($series); $i++) {
            $deltaDepth = $series[$i]['depth'] - $series[$i - 1]['depth'];
            $deltaTime = $series[$i]['minute'] - $series[$i - 1]['minute'];
            $rate = $deltaTime > 0 ? round(abs($deltaDepth / $deltaTime), 2) : 0.0;

            if ($deltaDepth < 0) {
                $ascent[] = ['minute' => $series[$i]['minute'], 'rate' => $rate];
                $descent[] = ['minute' => $series[$i]['minute'], 'rate' => 0.0];
            } elseif ($deltaDepth > 0) {
                $ascent[] = ['minute' => $series[$i]['minute'], 'rate' => 0.0];
                $descent[] = ['minute' => $series[$i]['minute'], 'rate' => $rate];
            } else {
                $ascent[] = ['minute' => $series[$i]['minute'], 'rate' => 0.0];
                $descent[] = ['minute' => $series[$i]['minute'], 'rate' => 0.0];
            }
        }

        return [$ascent, $descent];
    }
}
