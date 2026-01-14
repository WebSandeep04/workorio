<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SeedTenantCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:seed-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed countries table for every tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->info("Seeding countries for tenant {$tenant->name} ({$tenant->id})");

            DB::purge('tenant');

            Config::set('database.connections.tenant.database', $tenant->database);
            DB::reconnect('tenant');

            DB::setDefaultConnection('tenant');

            $this->call('db:seed', [
                '--class' => \Database\Seeders\TenantCountrySeeder::class,
                '--force' => true,
            ]);
        }

        DB::setDefaultConnection(config('database.default'));

        $this->info('Completed seeding countries for all tenants.');
    }
}
