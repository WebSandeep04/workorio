<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Illuminate\Support\Facades\Hash;

class ResetTenantAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:tenant-admin-password {tenant_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset tenant admin password to default';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found!");
            return 1;
        }
        
        // Create tenant connection
        if (!TenantDatabaseService::connectionExists($tenant->id)) {
            TenantDatabaseService::createConnection($tenant);
        }
        
        // Set tenant connection
        TenantDatabaseService::setDefaultConnection($tenant->id);
        
        $adminEmail = "admin@tenant{$tenantId}.com";
        $user = User::where('email', $adminEmail)->first();
        
        if (!$user) {
            $this->error("Admin user not found in tenant {$tenantId}!");
            $this->info("Expected email: {$adminEmail}");
            return 1;
        }
        
        $user->password = Hash::make('admin123');
        $user->save();
        
        $this->info("Tenant admin password reset successfully!");
        $this->info("Tenant: {$tenant->name}");
        $this->info("Email: {$adminEmail}");
        $this->info("Password: admin123");
        
        return 0;
    }
}
