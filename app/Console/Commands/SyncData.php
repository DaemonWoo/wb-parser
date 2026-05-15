<?php

namespace App\Console\Commands;

use App\Services\SyncService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

#[Signature('app:sync-data')]
#[Description('Sync WB API data')]
class SyncData extends Command
{
    public function handle(SyncService $syncService): int
    {
        $this->info('Syncing...');
        $start = microtime(true);

        $count = $syncService->syncSales($this->output);

        $count += $syncService->syncIncomes($this->output);

        $count += $syncService->syncStocks($this->output);

        $count += $syncService->syncOrders($this->output);

        $end = microtime(true);
        $duration = round($end - $start, 3);

        $this->info("✅ Готово за {$duration} сек.");
        $this->info("Synced: {$count}");

        return CommandAlias::SUCCESS;
    }
}
