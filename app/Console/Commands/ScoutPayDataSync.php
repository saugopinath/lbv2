<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScoutPayDataSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:payment-data-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Payment data to Scout';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data sync...');

        $models = [
            'App\\Models\\BenPaymentDetailsLB',
            'App\\Models\\BenPaymentDetailsJB',
            'App\\Models\\BenTransactionDetailsLB',
            'App\\Models\\BenTransactionDetailsJB',
            'App\\Models\\BenFailedPaymentDetailsLB',
            'App\\Models\\BenFailedPaymentDetailsJB',
        ];

        foreach ($models as $model) {
            $this->info("Flushing: {$model}");
            $this->call('scout:flush', ['model' => $model]);

            $this->info("Importing: {$model}");
            $this->call('scout:import', ['model' => $model]);
        }

        $this->info('Data sync completed.');
    }
}
