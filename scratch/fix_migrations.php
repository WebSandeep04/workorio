<?php
$files = glob('database/migrations/*.php');

$masterTables = [
    'tenants', 
    'tenant_databases', 
    'subscriptions', 
    'plans', 
    'features', 
    'plan_features', 
    'tenant_subscriptions', 
    'payment_histories', 
    'invoices',
    'admin_users',
    'msg91_settings_master'
];

$bothTables = [
    'users', 
    'password_reset_tokens', 
    'personal_access_tokens', 
    'failed_jobs', 
    'migrations', 
    'jobs', 
    'sessions',
    'cache',
    'cache_locks'
];

$countTenant = 0;
$countMaster = 0;
$countBoth = 0;
$skipped = 0;
$unknown = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // If it already has some connection skip logic, skip it
    if (strpos($content, 'Schema::getConnection()->getName()') !== false) {
        $skipped++;
        continue;
    }

    // Determine table
    preg_match('/Schema::(?:create|table|dropIfExists)\(\s*\'([^\']+)\'/', $content, $matches);
    $table = $matches[1] ?? null;

    if (!$table) {
        $unknown++;
        echo "Could not determine table for $file\n";
        continue;
    }

    $isMaster = in_array($table, $masterTables);
    $isBoth = in_array($table, $bothTables);

    if ($isBoth) {
        $countBoth++;
        continue; // No skip logic needed
    }

    if ($isMaster) {
        $skipLogic = <<<PHP
        // Skip this migration if NOT running on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }
PHP;
        $countMaster++;
        echo "Master: $file ($table)\n";
    } else {
        $skipLogic = <<<PHP
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }
PHP;
        $countTenant++;
        echo "Tenant: $file ($table)\n";
    }

    // Insert into up()
    $content = preg_replace('/(public function up\(\)\s*:\s*void\s*\{)/', "$1\n$skipLogic", $content);
    
    // Insert into down()
    $content = preg_replace('/(public function down\(\)\s*:\s*void\s*\{)/', "$1\n$skipLogic", $content);

    file_put_contents($file, $content);
}

echo "\nSummary:\n";
echo "Added skip logic to $countTenant tenant migrations.\n";
echo "Added skip logic to $countMaster master migrations.\n";
echo "Skipped $countBoth migrations that apply to both.\n";
echo "Skipped $skipped migrations that already had skip logic.\n";
echo "Skipped $unknown migrations where table could not be identified.\n";
