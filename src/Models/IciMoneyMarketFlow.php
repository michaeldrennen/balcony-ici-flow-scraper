<?php

declare(strict_types=1);

namespace MichaelDrennen\Balcony\IciFlowScraper\Models;

use Illuminate\Database\Eloquent\Model;

final class IciMoneyMarketFlow extends Model
{
    protected $table = 'balcony_ici_money_market_flows';

    protected $guarded = ['id'];

    protected $casts = [
        'report_date' => 'date',
        'total_assets_billions' => 'decimal:3',
        'retail_assets_billions' => 'decimal:3',
        'institutional_assets_billions' => 'decimal:3',
        'government_assets_billions' => 'decimal:3',
        'prime_assets_billions' => 'decimal:3',
    ];
}
