<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Exception;

class MigrateTenantStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-tenant-storage {--dry-run : Only show what would be moved without moving anything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing files to tenant-isolated directories';

    /**
     * Filesystem disk to use.
     */
    protected $disk = 'public';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN: No files will be moved.');
        }

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->info("Processing Tenant ID: {$tenant->id} ({$tenant->tenant_code})");

            try {
                // Switch to tenant database
                TenantDatabaseService::setDefaultConnection($tenant->id);
                $connection = DB::getDefaultConnection();
                $this->line("  Connected to: {$connection}");

                // 1. Documents
                $this->migrateTable($tenant->id, 'documents', 'file_path', $dryRun);

                // 2. Employee Documents
                $this->migrateTable($tenant->id, 'employee_documents', 'file_path', $dryRun);

                // 3. Employee Profile Pictures
                $this->migrateTable($tenant->id, 'employees', 'profile_picture', $dryRun);

                // 4. Task Images
                $this->migrateTable($tenant->id, 'task_images', 'image_path', $dryRun);

                // 5. Petty Cash Attachments
                $this->migrateTable($tenant->id, 'petty_cash_datas', 'attachment', $dryRun);

                // 6. Project SOW Path
                $this->migrateTable($tenant->id, 'customer_projects', 'sow_path', $dryRun);

                // 7. Quotation Setup Logo
                $this->migrateTable($tenant->id, 'quotation_settings', 'logo_path', $dryRun);

            } catch (Exception $e) {
                $this->error("  Error processing tenant {$tenant->id}: " . $e->getMessage());
            }

            $this->line('---------------------------------------');
        }

        $this->info('Migration complete.');
    }

    /**
     * Migrate a specific table's file paths.
     */
    protected function migrateTable($tenantId, $tableName, $columnName, $dryRun)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable($tableName)) {
            $this->warn("  Table '{$tableName}' not found, skipping.");
            return;
        }

        $records = DB::table($tableName)->whereNotNull($columnName)->where($columnName, '!=', '')->get();

        if ($records->isEmpty()) {
            return;
        }

        $this->comment("  Processing table: {$tableName}");

        foreach ($records as $record) {
            $oldPath = $record->$columnName;

            // Skip if already in the new format (starts with 'tenants/')
            if (str_starts_with($oldPath, 'tenants/')) {
                continue;
            }

            $newPath = "tenants/{$tenantId}/" . ltrim($oldPath, '/');

            if (Storage::disk($this->disk)->exists($oldPath)) {
                $this->line("    Moving: {$oldPath} -> {$newPath}");

                if (!$dryRun) {
                    try {
                        // Ensure directory exists
                        $directory = dirname($newPath);
                        if (!Storage::disk($this->disk)->exists($directory)) {
                            Storage::disk($this->disk)->makeDirectory($directory);
                        }

                        // Move the file
                        Storage::disk($this->disk)->move($oldPath, $newPath);

                        // Update DB
                        DB::table($tableName)->where('id', $record->id)->update([
                            $columnName => $newPath
                        ]);
                    } catch (Exception $e) {
                        $this->error("    Failed to move {$oldPath}: " . $e->getMessage());
                    }
                }
            } else {
                $this->warn("    File NOT FOUND: {$oldPath}");
            }
        }
    }
}
