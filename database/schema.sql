CREATE TABLE IF NOT EXISTS balcony_ici_money_market_flows (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    report_date DATE NOT NULL,
    total_assets_billions DECIMAL(12,3) NOT NULL,
    retail_assets_billions DECIMAL(12,3) NOT NULL,
    institutional_assets_billions DECIMAL(12,3) NOT NULL,
    government_assets_billions DECIMAL(12,3) NOT NULL,
    prime_assets_billions DECIMAL(12,3) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY balcony_ici_report_date_unique (report_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
