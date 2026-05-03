<?php

namespace App\Transformers;

class OrdersTransformer extends BaseTransformer
{
    protected function allowedFields(): array
    {
        return [
            'g_number',
            'date',
            'last_change_date',
            'total_price',
            'discount_percent',
            'region_name',
            'is_cancel',
            'cancel_dt',
            'income_id',
        ];
    }

    protected function shouldSkip(array $item): bool
    {
        return empty($item['nm_id']) || empty($item['g_number']);
    }

    protected function warehouseName(array $item): string
    {
        return $item['warehouse_name'] ?? 'UNKNOWN';
    }

    protected function dateValue(array $item): string
    {
        return $item['date'] ?? now()->toDateString();
    }

    protected function fieldsToUnset(): array
    {
        return ['date'];
    }

    protected function logSkippedItem(array $item): void
    {
        $this->logMissingRequired('Income skipped: missing required fields', $item);
    }
}
