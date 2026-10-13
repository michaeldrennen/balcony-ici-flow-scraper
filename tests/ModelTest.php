<?php

declare(strict_types=1);

namespace MichaelDrennen\Balcony\IciFlowScraper\Tests;

use DateTimeImmutable;
use MichaelDrennen\Balcony\IciFlowScraper\DTO\IciMmfFlowDTO;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    public function testDtoMapsToModelAttributes(): void
    {
        $dto = new IciMmfFlowDTO(new DateTimeImmutable('2026-10-07'), 6450.21, 2610.15, 3840.06, 5310.80, 1025.40);

        $this->assertSame([
            'report_date' => '2026-10-07',
            'total_assets_billions' => 6450.21,
            'retail_assets_billions' => 2610.15,
            'institutional_assets_billions' => 3840.06,
            'government_assets_billions' => 5310.80,
            'prime_assets_billions' => 1025.40,
        ], $dto->asModelAttributes());
    }

    public function testEloquentModelAcceptsDtoAttributes(): void
    {
        if (!class_exists('Illuminate\Database\Eloquent\Model')) {
            $this->markTestSkipped('illuminate/database is not installed.');
        }

        // Eloquent's guarded-attribute check reads the column listing, so the model needs a connection.
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $dto = new IciMmfFlowDTO(new DateTimeImmutable('2026-10-07'), 1.0, 2.0, 3.0, 4.0, 5.0);
        $model = new \MichaelDrennen\Balcony\IciFlowScraper\Models\IciMoneyMarketFlow($dto->asModelAttributes());

        $this->assertSame('balcony_ici_money_market_flows', $model->getTable());
        $this->assertSame(['id'], $model->getGuarded());
    }
}
