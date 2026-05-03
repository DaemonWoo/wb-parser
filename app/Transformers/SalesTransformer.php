<?php

namespace App\Transformers;

class SalesTransformer extends BaseTransformer
{
    protected function allowedFields(): array
    {
        return [
            'sale_id',
            'last_change_date',
            'spp',
            'g_number',
            'oblast_okrug_name',
            'country_name',
            'region_name',
            'price_with_disc',
            'for_pay',
            'total_price',
            'discount_percent',
            'finished_price',
            'is_storno',
            'is_supply',
            'is_realization',
            'income_id',
        ];
    }

    protected function shouldSkip(array $item): bool
    {
        return ! isset($item['nm_id']);
    }

    protected function warehouseName(array $item): string
    {
        return $item['warehouse_name'] ?? 'UNKNOWN';
    }

    protected function dateValue(array $item): string
    {
        return $item['date'] ?? now()->toDateString();
    }

    protected function logSkippedItem(array $item): void
    {
        $this->logMissingRequired('Income skipped: missing required fields', $item);
    }
}
