<?php

declare(strict_types=1);

namespace MichaelDrennen\Balcony\IciFlowScraper;

final class Config
{
    public const DEFAULT_URL = 'https://www.ici.org/research/stats/mmf';

    public function __construct(
        public readonly string $url = self::DEFAULT_URL,
        public readonly float $timeoutSeconds = 20.0,
    ) {
    }
}
