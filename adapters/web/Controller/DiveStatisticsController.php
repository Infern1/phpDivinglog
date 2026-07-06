<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\CertificationRepository;
use PhpDivingLog\Repository\DiveStatisticsRepository;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\DiveStatisticsFormatter;
use PhpDivingLog\Support\Formatter;
use PhpDivingLog\Support\MediaResolver;
use PhpDivingLog\Support\UnitConverter;

final readonly class DiveStatisticsController
{
    public function __construct(
        private DiveStatisticsRepository $statistics,
        private CertificationRepository $certifications,
        private DiveStatisticsFormatter $statisticsFormatter,
        private Formatter $formatter,
        private UnitConverter $converter,
        private MediaResolver $media,
        private Config $config,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function view(): array
    {
        $stats = $this->statistics->compute();

        $depthDistribution = $this->buildDepthDistribution($stats->depthBuckets, $stats->totalDives);
        $classificationRows = [
            ['label' => 'Shore dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['shore'] ?? null, $stats->totalDives)],
            ['label' => 'Boat dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['boat'] ?? null, $stats->totalDives)],
            ['label' => 'Night dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['night'] ?? null, $stats->totalDives)],
            ['label' => 'Drift dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['drift'] ?? null, $stats->totalDives)],
            ['label' => 'Deep dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['deep'] ?? null, $stats->totalDives)],
            ['label' => 'Cave dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['cave'] ?? null, $stats->totalDives)],
            ['label' => 'Wreck dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['wreck'] ?? null, $stats->totalDives)],
            ['label' => 'Photo dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['photo'] ?? null, $stats->totalDives)],
            ['label' => 'Saltwater dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['salt'] ?? null, $stats->totalDives)],
            ['label' => 'Freshwater dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['fresh'] ?? null, $stats->totalDives)],
            ['label' => 'Brackish dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['brackish'] ?? null, $stats->totalDives)],
            ['label' => 'Deco dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['deco'] ?? null, $stats->totalDives)],
            ['label' => 'No-deco dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['nodeco'] ?? null, $stats->totalDives)],
            ['label' => 'Repetitive dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['rep'] ?? null, $stats->totalDives)],
            ['label' => 'Non-repetitive dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['norep'] ?? null, $stats->totalDives)],
            ['label' => 'Single cylinder', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['single'] ?? null, $stats->totalDives)],
            ['label' => 'Twin cylinder', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['twin'] ?? null, $stats->totalDives)],
            ['label' => 'Open circuit', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['oc'] ?? null, $stats->totalDives)],
            ['label' => 'SCR dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['scr'] ?? null, $stats->totalDives)],
            ['label' => 'CCR dives', 'value' => $this->statisticsFormatter->percentageLabel($stats->classifications['ccr'] ?? null, $stats->totalDives)],
        ];

        $certificationRows = [];
        if ($this->config->userShowCerts()) {
            foreach ($this->certifications->listAll() as $certification) {
                $certificationRows[] = [
                    'organisation' => $certification->organisation,
                    'certification' => $certification->certification,
                    'date' => $certification->date !== null ? $this->formatter->formatDate($certification->date) : null,
                    'cert_number' => $certification->certNumber,
                    'instructor' => $certification->instructor,
                    'front_image_url' => $certification->frontImage !== null ? $this->media->userUrl($certification->frontImage) : null,
                    'back_image_url' => $certification->backImage !== null ? $this->media->userUrl($certification->backImage) : null,
                ];
            }
        }

        return [
            'total_dives' => $stats->totalDives,
            'first_dive' => [
                'date' => $stats->firstDiveDate !== null ? $this->formatter->formatDate($stats->firstDiveDate) : '-',
                'number' => $stats->firstDiveNumber,
            ],
            'last_dive' => [
                'date' => $stats->lastDiveDate !== null ? $this->formatter->formatDate($stats->lastDiveDate) : '-',
                'number' => $stats->lastDiveNumber,
            ],
            'bottom_time' => $this->statisticsFormatter->bottomTime($stats->totalBottomTimeMinutes),
            'dive_time' => [
                'longest' => $this->statisticsFormatter->duration($stats->diveTime['max']),
                'longest_number' => $stats->diveTime['maxNumber'],
                'shortest' => $this->statisticsFormatter->duration($stats->diveTime['min']),
                'shortest_number' => $stats->diveTime['minNumber'],
                'average' => $this->statisticsFormatter->duration($stats->diveTime['avg'] !== null ? (int) round($stats->diveTime['avg']) : null),
            ],
            'depth' => [
                'deepest' => $this->statisticsFormatter->depth($stats->depth['max']),
                'deepest_number' => $stats->depth['maxNumber'],
                'shallowest' => $this->statisticsFormatter->depth($stats->depth['min']),
                'shallowest_number' => $stats->depth['minNumber'],
                'average' => $this->statisticsFormatter->depth($stats->depth['avg']),
            ],
            'water_temp' => [
                'coldest' => $this->statisticsFormatter->temperature($stats->waterTemp['min']),
                'coldest_number' => $stats->waterTemp['minNumber'],
                'warmest' => $this->statisticsFormatter->temperature($stats->waterTemp['max']),
                'warmest_number' => $stats->waterTemp['maxNumber'],
                'average' => $this->statisticsFormatter->temperature($stats->waterTemp['avg']),
            ],
            'air_temp' => [
                'coldest' => $this->statisticsFormatter->temperature($stats->airTemp['min']),
                'coldest_number' => $stats->airTemp['minNumber'],
                'warmest' => $this->statisticsFormatter->temperature($stats->airTemp['max']),
                'warmest_number' => $stats->airTemp['maxNumber'],
                'average' => $this->statisticsFormatter->temperature($stats->airTemp['avg']),
            ],
            'classification_rows' => $classificationRows,
            'certification_rows' => $certificationRows,
            'depth_distribution' => $depthDistribution,
            'depth_distribution_json' => json_encode($depthDistribution, JSON_THROW_ON_ERROR),
            'depth_distribution_unit' => $this->converter->depthLabel(),
        ];
    }

    /**
     * @param array{b0_18:int,b19_30:int,b31_40:int,b41_55:int,b55_plus:int} $buckets
     * @return list<array{label:string,count:int,percent:int}>
     */
    private function buildDepthDistribution(array $buckets, int $total): array
    {
        $labels = [
            'b0_18' => $this->bucketLabel(0.0, 18.0),
            'b19_30' => $this->bucketLabel(19.0, 30.0),
            'b31_40' => $this->bucketLabel(31.0, 40.0),
            'b41_55' => $this->bucketLabel(41.0, 55.0),
            'b55_plus' => $this->bucketLabel(56.0, null),
        ];

        return [
            $this->bucketPayload($labels['b0_18'], $buckets['b0_18'], $total),
            $this->bucketPayload($labels['b19_30'], $buckets['b19_30'], $total),
            $this->bucketPayload($labels['b31_40'], $buckets['b31_40'], $total),
            $this->bucketPayload($labels['b41_55'], $buckets['b41_55'], $total),
            $this->bucketPayload($labels['b55_plus'], $buckets['b55_plus'], $total),
        ];
    }

    /**
     * @return array{label:string,count:int,percent:int}
     */
    private function bucketPayload(string $label, int $count, int $total): array
    {
        $percent = $total > 0 ? (int) round(($count / $total) * 100) : 0;

        return [
            'label' => $label,
            'count' => $count,
            'percent' => $percent,
        ];
    }

    private function bucketLabel(float $minMetric, ?float $maxMetric): string
    {
        $unit = $this->converter->depthLabel();
        if ($unit === 'm') {
            if ($maxMetric === null) {
                return sprintf('>%d %s', (int) $minMetric - 1, $unit);
            }

            return sprintf('%d-%d %s', (int) $minMetric, (int) $maxMetric, $unit);
        }

        $minDisplay = (int) round($this->converter->depthToDisplay($minMetric));
        if ($maxMetric === null) {
            return sprintf('>%d %s', $minDisplay - 1, $unit);
        }

        $maxDisplay = (int) round($this->converter->depthToDisplay($maxMetric));
        return sprintf('%d-%d %s', $minDisplay, $maxDisplay, $unit);
    }
}
