<?php

namespace App\Services;

use App\Models\Income;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Stock;
use App\Transformers\IncomeTransformer;
use App\Transformers\OrdersTransformer;
use App\Transformers\SalesTransformer;
use App\Transformers\StocksTransformer;
use Carbon\Carbon;
use Illuminate\Console\OutputStyle;

class SyncService
{
    public function __construct(
        protected ApiFetcherService $api,
        protected SalesTransformer $salesTransformer,
        protected IncomeTransformer $incomesTransformer,
        protected StocksTransformer $stocksTransformer,
        protected OrdersTransformer $ordersTransformer,
    ) {}

    public function syncSales(OutputStyle $output): int
    {
        return $this->sync(
            endpoint: 'sales',
            model: Sale::class,
            uniqueKeys: ['sale_id'],
            params: [
                'dateFrom' => '2026-04-30',
                'dateTo' => '2026-04-30',
            ],
            output: $output,
            transform: fn ($items) => $this->salesTransformer->transform($items)
        );
    }

    public function syncIncomes(OutputStyle $output): int
    {
        return $this->sync(
            endpoint: 'incomes',
            model: Income::class,
            uniqueKeys: ['income_id', 'product_id'],
            params: [
                'dateFrom' => '2026-02-28',
                'dateTo' => '2026-04-30',
            ],
            output: $output,
            transform: fn ($items) => $this->incomesTransformer->transform($items)
        );
    }

    public function syncStocks(OutputStyle $output): int
    {
        $today = Carbon::today()->format('Y-m-d');

        return $this->sync(
            endpoint: 'stocks',
            model: Stock::class,
            uniqueKeys: ['product_id', 'warehouse_id', 'date_id'],
            params: [
                'dateFrom' => $today,
            ],
            output: $output,
            transform: fn ($items) => $this->stocksTransformer->transform($items)
        );
    }

    public function syncOrders(OutputStyle $output): int
    {
        return $this->sync(
            endpoint: 'orders',
            model: Order::class,
            uniqueKeys: ['g_number', 'product_id', 'date_id'],
            params: [
                'dateFrom' => '2026-03-28',
                'dateTo' => '2026-04-30',
            ],
            output: $output,
            transform: fn ($items) => $this->ordersTransformer->transform($items)
        );
    }

    private function sync(
        string $endpoint,
        string $model,
        array $uniqueKeys,
        array $params,
        OutputStyle $output,
        callable $transform
    ): int {
        $page = 1;
        $total = 0;

        $totalPages = $this->api->getTotalPages($endpoint, $params);

        do {
            $items = $this->api->fetchPage($endpoint, $params, $page);

            if ($items === null) {
                $output->error("Request failed on page {$page}");

                return $total;
            }

            if (empty($items)) {
                $output->warning("Empty response on page {$page}");
                break;
            }

            $rows = $transform($items);

            if (! empty($rows)) {
                $updateColumns = array_diff(
                    array_keys($rows[0]),
                    array_merge($uniqueKeys, ['created_at'])
                );
                $model::upsert(
                    $rows,
                    $uniqueKeys,
                    $updateColumns
                );
            }

            $total += count($items);
            $page++;
        } while ($page <= $totalPages);

        return $total;
    }
}
