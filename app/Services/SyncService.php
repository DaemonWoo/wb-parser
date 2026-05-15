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
use Illuminate\Database\Eloquent\Model;

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
            transform: fn($items) => $this->salesTransformer->transform($items),
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
            transform: fn($items) => $this->incomesTransformer->transform($items),
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
            transform: fn($items) => $this->stocksTransformer->transform($items),
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
            transform: fn($items) => $this->ordersTransformer->transform($items),
        );
    }

    /**
     * @param class-string<Model> $model
     */
    private function sync(
        string $endpoint,
        string $model,
        array $uniqueKeys,
        array $params,
        OutputStyle $output,
        callable $transform,
    ): int {
        $page = 1;
        $totalItems = 0;
        $totalPages = $this->api->getTotalPages($endpoint, $params);
        $concurrency = 10;

        $output->progressStart($totalPages);

        $endPage = min($page + $concurrency - 1, $totalPages);
        while ($page <= $totalPages) {
            $pages = range($page, $endPage);

            $pagesData = $this->api->fetchPagesParallel(
                endpoint: $endpoint,
                params: $params,
                startPage: $page,
                endPage: $endPage,
                concurrency: $concurrency,
            );

            foreach ($pagesData as $items) {
                $rows = $transform($items);
                $updateColumns = array_diff(array_keys($rows[0]), ['created_at', ...$uniqueKeys]);

                $model::upsert($rows, $uniqueKeys, $updateColumns);

                $totalItems += count($items);
            }

            $output->progressAdvance(count($pages));
            $page += $concurrency;
        }

        $output->progressFinish();

        return $totalItems;
    }
}
