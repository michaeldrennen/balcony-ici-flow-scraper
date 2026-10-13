<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('balcony_ici_money_market_flows', function (Blueprint $table): void {
            $table->id();
            $table->date('report_date')->unique('balcony_ici_report_date_unique');
            $table->decimal('total_assets_billions', 12, 3);
            $table->decimal('retail_assets_billions', 12, 3);
            $table->decimal('institutional_assets_billions', 12, 3);
            $table->decimal('government_assets_billions', 12, 3);
            $table->decimal('prime_assets_billions', 12, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balcony_ici_money_market_flows');
    }
};
