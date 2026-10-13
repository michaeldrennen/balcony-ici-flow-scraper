<?php

declare(strict_types=1);

namespace MichaelDrennen\Balcony\IciFlowScraper\DTO;

use DateTimeImmutable;

final class IciMmfFlowDTO implements MetricObservation
{
    public function __construct(
        public readonly DateTimeImmutable $reportDate,
        public readonly float $totalAssetsBillions,
        public readonly float $retailAssetsBillions,
        public readonly float $institutionalAssetsBillions,
        public readonly float $governmentAssetsBillions,
        public readonly float $primeAssetsBillions,
    ) {
    }

    public function asModelAttributes(): array
    {
        return [
            'report_date' => $this->reportDate->format('Y-m-d'),
            'total_assets_billions' => $this->totalAssetsBillions,
            'retail_assets_billions' => $this->retailAssetsBillions,
            'institutional_assets_billions' => $this->institutionalAssetsBillions,
            'government_assets_billions' => $this->governmentAssetsBillions,
            'prime_assets_billions' => $this->primeAssetsBillions,
        ];
    }
}
