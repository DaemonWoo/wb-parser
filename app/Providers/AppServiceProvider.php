<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //        DB::whenQueryingForLongerThan(500, function (Connection $connection, QueryExecuted $event) {
        //            $this->info('Query takes longer that 500ms!');
        //        });
    }
}
