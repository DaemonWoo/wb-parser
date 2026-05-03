<?php

namespace App\Services;

use App\Models\DimDate;
use App\Models\Product;
use App\Models\Warehouse;
use Carbon\Carbon;

class DimensionService
{
    protected array $products = [];

    protected array $warehouses = [];

    protected array $dates = [];

    public function preload(array $items): void
    {
        $now = now();

        // =========================
        // PRODUCTS
        // =========================

        $nmIds = [];
        $source = [];

        foreach ($items as $item) {
            if (! isset($item['nm_id'])) {
                continue;
            }

            $nmId = (int) trim($item['nm_id']);

            $nmIds[$nmId] = true;

            // сохраняем любой первый item для этого nm_id
            if (! isset($source[$nmId])) {
                $source[$nmId] = $item;
            }
        }

        $nmIds = array_keys($nmIds);

        if (! empty($nmIds)) {

            // существующие
            $existing = Product::whereIn('nm_id', $nmIds)
                ->get()
                ->keyBy(fn ($p) => (int) $p->nm_id);

            // недостающие
            $missing = array_diff($nmIds, $existing->keys()->all());

            if (! empty($missing)) {

                $rows = [];

                foreach ($missing as $nmId) {
                    $item = $source[$nmId];

                    $rows[] = [
                        'nm_id' => $nmId,
                        'barcode' => $item['barcode'] ?? null,
                        'supplier_article' => $item['supplier_article'] ?? null,
                        'tech_size' => $item['tech_size'] ?? null,
                        'brand' => $item['brand'] ?? null,
                        'category' => $item['category'] ?? null,
                        'subject' => $item['subject'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                Product::insert($rows);
            }

            // финальный кеш (ВСЕГДА после insert)
            $this->products = Product::whereIn('nm_id', $nmIds)
                ->get()
                ->keyBy(fn ($p) => (int) $p->nm_id)
                ->all();
        }

        // =========================
        // WAREHOUSES
        // =========================

        $names = [];

        foreach ($items as $item) {
            if (empty($item['warehouse_name'])) {
                continue;
            }

            $names[$item['warehouse_name']] = true;
        }

        $names = array_keys($names);

        if (! empty($names)) {

            $existing = Warehouse::whereIn('name', $names)
                ->get()
                ->keyBy('name');

            $missing = array_diff($names, $existing->keys()->all());

            if (! empty($missing)) {

                $rows = [];

                foreach ($missing as $name) {
                    $rows[] = [
                        'name' => $name,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                Warehouse::insert($rows);
            }

            $this->warehouses = Warehouse::whereIn('name', $names)
                ->get()
                ->keyBy('name')
                ->all();
        }

        // =========================
        // DATES
        // =========================

        $dates = [];

        foreach ($items as $item) {
            if (empty($item['date'])) {
                continue;
            }

            $date = Carbon::parse($item['date'])->toDateString();
            $dates[$date] = true;
        }

        $dates = array_keys($dates);

        if (! empty($dates)) {

            $existing = DimDate::whereIn('date', $dates)
                ->get()
                ->keyBy('date');

            $missing = array_diff($dates, $existing->keys()->all());

            if (! empty($missing)) {

                $rows = [];

                foreach ($missing as $date) {
                    $c = Carbon::parse($date);

                    $rows[] = [
                        'date' => $date,
                        'year' => $c->year,
                        'month' => $c->month,
                        'day' => $c->day,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                DimDate::insert($rows);
            }

            $this->dates = DimDate::whereIn('date', $dates)
                ->get()
                ->keyBy('date')
                ->all();
        }
    }

    /**
     * PRODUCT
     */
    public function product(array $item): Product
    {
        return $this->products[$item['nm_id']];
    }

    /**
     * WAREHOUSE
     */
    public function warehouse(string $name): Warehouse
    {
        return $this->warehouses[$name];
    }

    /**
     * DATE (DIM)
     */
    public function date(string $date): DimDate
    {
        $date = Carbon::parse($date)->toDateString();

        return $this->dates[$date];
    }
}
