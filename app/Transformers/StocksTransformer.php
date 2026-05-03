<?php

namespace App\Transformers;

class StocksTransformer extends BaseTransformer
{
    protected function allowedFields(): array
    {
        return [
            'last_change_date',
            'quantity',
            'quantity_full',
            'in_way_to_client',
            'in_way_from_client',
            'price',
            'discount',
            'is_supply',
            'is_realization',
            'sc_code',
        ];
    }

    protected function shouldSkip(array $item): bool
    {
        return empty($item['nm_id']) || empty($item['warehouse_name']) || empty($item['date']);
    }

    protected function dateValue(array $item): string
    {
        return $item['date'];
    }
}
