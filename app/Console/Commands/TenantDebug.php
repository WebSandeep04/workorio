<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;

class TenantDebug extends Command
{
    protected $signature = 'tenant:debug {tenantId} {--email=} {--reset-password=} {--verify-password=}';

    protected $description = 'Debug tenant database connection and users table; optionally reset a user password.';

    public function handle(): int
    {
        $tenantId = (int) $this->argument('tenantId');
        $email = $this->option('email');
        $resetPassword = $this->option('reset-password');
        $verifyPassword = $this->option('verify-password');

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant not found: {$tenantId}");
            return self::FAILURE;
        }

        $this->info("Tenant found: ID={$tenant->id}");

        // Create/ensure connection
        TenantDatabaseService::createConnection($tenant);
        $connectionName = TenantDatabaseService::getConnectionName($tenant->id);
        $this->info("Connection name: {$connectionName}");

        // Basic connectivity
        try {
            $ok = DB::connection($connectionName)->select('select 1 as ok');
            $this->info('Connectivity check: ' . json_encode($ok));
        } catch (\Throwable $e) {
            $this->error('Connectivity failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Migrations table
        try {
            $migrations = DB::connection($connectionName)->table('migrations')->count();
            $this->info("Migrations count: {$migrations}");
        } catch (\Throwable $e) {
            $this->warn('Migrations table not found or inaccessible: ' . $e->getMessage());
        }

        // Users checks
        try {
            $userCount = DB::connection($connectionName)->table('users')->count();
            $this->info("Users count: {$userCount}");
        } catch (\Throwable $e) {
            $this->error('Users table error: ' . $e->getMessage());
            return self::FAILURE;
        }

        if ($email) {
            $user = DB::connection($connectionName)->table('users')->where('email', $email)->first();
            if ($user) {
                $this->info('User found: ' . json_encode(['id' => $user->id, 'email' => $user->email, 'role_id' => $user->role_id]));
                $hash = DB::connection($connectionName)->table('users')->where('id', $user->id)->value('password');
                $this->line('Password hash starts with: ' . substr((string) $hash, 0, 4));

                if (!empty($verifyPassword)) {
                    $hashOk = \Hash::check($verifyPassword, (string) $hash);
                    $this->info('Verify password match: ' . ($hashOk ? 'true' : 'false'));
                }

                if (!empty($resetPassword)) {
                    DB::connection($connectionName)->table('users')->where('id', $user->id)->update([
                        'password' => bcrypt($resetPassword),
                    ]);
                    $this->info('Password reset applied.');
                }
            } else {
                $this->warn("No user with email {$email} in tenant {$tenantId}");
            }
        }

        return self::SUCCESS;
    }
}


