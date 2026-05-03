<?php

namespace App\Transformers;

class IncomeTransformer extends BaseTransformer
{
    protected function allowedFields(): array
    {
        return [
            'income_id',
            'last_change_date',
            'date_close',
            'quantity',
            'total_price',
        ];
    }

    protected function shouldSkip(array $item): bool
    {
        return ! isset($item['income_id']) || ! isset($item['nm_id']);
    }

    protected function warehouseName(array $item): string
    {
        return $item['warehouse_name'];
    }

    protected function dateValue(array $item): string
    {
        return $item['date'];
    }

    protected function logSkippedItem(array $item): void
    {
        $this->logMissingRequired('Income skipped: missing required fields', $item);
    }
}
