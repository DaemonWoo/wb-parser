<?php

namespace App\Transformers;

use App\Services\DimensionService;
use Illuminate\Support\Facades\Log;

abstract class BaseTransformer
{
    public function __construct(
        protected DimensionService $dim
    ) {}

    public function transform(array $items): array
    {
        $this->dim->preload($items);

        $rows = [];
        $now = now();
        $flipped = array_flip($this->allowedFields());

        foreach ($items as $item) {
            if ($this->shouldSkip($item)) {
                $this->logSkippedItem($item);

                continue;
            }

            $product = $this->dim->product($item);
            $warehouse = $this->dim->warehouse($this->warehouseName($item));
            $date = $this->dim->date($this->dateValue($item));

            $row = array_intersect_key($item, $flipped);

            foreach ($this->fieldsToUnset() as $field) {
                unset($row[$field]);
            }

            $row['product_id'] = $product->id;
            $row['warehouse_id'] = $warehouse->id;
            $row['date_id'] = $date->id;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;

            $rows[] = $row;
        }

        return $rows;
    }

    abstract protected function allowedFields(): array;

    abstract protected function shouldSkip(array $item): bool;

    protected function warehouseName(array $item): string
    {
        return $item['warehouse_name'] ?? 'UNKNOWN';
    }

    abstract protected function dateValue(array $item): string;

    protected function fieldsToUnset(): array
    {
        return [];
    }

    protected function logSkippedItem(array $item): void {}

    protected function logMissingRequired(string $message, array $item): void
    {
        Log::warning($message, [
            'income_id' => $item['income_id'] ?? null,
            'nm_id' => $item['nm_id'] ?? null,
            'item' => $item,
        ]);
    }
}
