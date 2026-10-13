# balcony-ici-flow-scraper

PHP 8.1+ scraper for the Investment Company Institute weekly money market fund asset tables, part of The Balcony macro flow system.

## Usage (framework-agnostic)

```php
use MichaelDrennen\Balcony\IciFlowScraper\Client;
use MichaelDrennen\Balcony\IciFlowScraper\Config;

$client = new Client(new Config());

foreach ($client->fetchFlows() as $dto) {
    // $dto->reportDate, $dto->totalAssetsBillions, $dto->retailAssetsBillions, ...
}
```

Rows that do not have a date plus six numeric cells are skipped, which drops header rows automatically. Values are in billions of dollars with thousands separators removed.

## Laravel persistence

```php
use MichaelDrennen\Balcony\IciFlowScraper\Models\IciMoneyMarketFlow;

foreach ($client->fetchFlows() as $dto) {
    IciMoneyMarketFlow::updateOrCreate(
        ['report_date' => $dto->reportDate->format('Y-m-d')],
        $dto->asModelAttributes(),
    );
}
```

## Errors

Failures throw `MichaelDrennen\Balcony\IciFlowScraper\Exceptions\BalconyClientException`. HTTP 429 sets `isRateLimited()` and exposes `retryAfterSeconds`.

## Tests

```
composer install
vendor/bin/phpunit
```
