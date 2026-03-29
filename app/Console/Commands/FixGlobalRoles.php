<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UserRoleSchemeOfficeMapping;

class FixGlobalRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:fix-global';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate global (null scheme_id) roles to scheme-specific roles based on mappings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix global roles...');

        $globalRoles = DB::table('model_has_roles')
            ->whereNull('scheme_id')
            ->get();

        foreach ($globalRoles as $entry) {
            $mappings = UserRoleSchemeOfficeMapping::where('user_id', $entry->model_id)
                ->where('role_id', $entry->role_id)
                ->get();

            if ($mappings->count() > 0) {
                $this->info("Found mappings for User ID {$entry->model_id}, Role ID {$entry->role_id}");
                
                // Delete the global one
                DB::table('model_has_roles')
                    ->where('role_id', $entry->role_id)
                    ->where('model_id', $entry->model_id)
                    ->where('model_type', $entry->model_type)
                    ->whereNull('scheme_id')
                    ->delete();

                foreach ($mappings as $mapping) {
                    // Try to insert with scheme_id
                    try {
                        DB::table('model_has_roles')->updateOrInsert([
                            'role_id' => $entry->role_id,
                            'model_id' => $entry->model_id,
                            'model_type' => $entry->model_type,
                            'scheme_id' => $mapping->scheme_id,
                        ]);
                        $this->line(" - Assigned to Scheme ID: {$mapping->scheme_id}");
                    } catch (\Exception $e) {
                        $this->error(" - Error assigning to Scheme ID: {$mapping->scheme_id}");
                    }
                }
            } else {
                $this->warn("No mapping found for User ID {$entry->model_id}, Role ID {$entry->role_id}. Keeping as global.");
            }
        }

        $this->info('Fix completed.');
    }
}
