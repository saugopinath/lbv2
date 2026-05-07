<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScoutDataSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:data-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data to Scout';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data sync...');

        $models = [
            'App\\Models\\BeneficiaryPersonalDetail',
            'App\\Models\\BeneficiaryContactDetail',
            'App\\Models\\BeneficiaryBankDetail',
            'App\\Models\\BeneficiaryEnclosure',
            'App\\Models\\BeneficiaryAadhaar',
            'App\\Models\\BeneficiarySelfDeclaration',
            'App\\Models\\AcceptRejectInfo',
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
