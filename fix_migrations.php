<?php

$tenantMigrations = [
    '2026_04_01_120400_add_user_id_to_external_lead_followups_table.php',
    '2026_04_10_034101_create_whatsapp_templates_table.php',
    '2026_04_15_101602_create_calling_assign_logs_table.php',
    '2026_04_15_101606_add_is_assigned_to_calling_campaign_calling_table.php',
    '2026_04_21_065857_add_terms_and_conditions_to_payment_terms_table.php',
    '2026_04_23_063101_add_week_offs_to_shifts_table.php',
    '2026_04_23_095536_add_working_type_to_employees_table.php',
    '2026_04_23_102100_add_is_paid_to_leave_types_table.php',
    '2026_04_24_104226_add_half_days_to_shifts_table.php',
    '2026_04_24_121500_add_hours_cols_to_shifts_table.php',
    '2026_05_06_071003_add_is_attendance_to_users_table.php',
    '2026_07_18_115711_add_grant_comp_off_to_shifts_table.php',
    '2026_07_23_120646_add_deductions_to_payroll_details_table.php',
    '2026_07_24_062454_drop_salary_type_from_salary_structures_table.php'
];

$masterMigrations = [
    '2025_09_16_090000_alter_tenant_databases_add_connection_cols.php',
    '2026_07_21_124630_add_is_payroll_enabled_to_tenant_databases_table.php',
    '2026_07_21_124652_add_payroll_flags_to_tenants_table.php'
];

$tenantCheck = "        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() === 'mysql') { return; }\n";
$masterCheck = "        if (\Illuminate\Support\Facades\Schema::getConnection()->getName() !== 'mysql') { return; }\n";

foreach ($tenantMigrations as $file) {
    $path = 'database/migrations/' . $file;
    $content = file_get_contents($path);
    if (!str_contains($content, 'getConnection()->getName()')) {
        $content = preg_replace('/(public function up\(\)(?:\s*:\s*void)?\s*\{)/', "$1\n" . $tenantCheck, $content);
        $content = preg_replace('/(public function down\(\)(?:\s*:\s*void)?\s*\{)/', "$1\n" . $tenantCheck, $content);
        file_put_contents($path, $content);
        echo "Updated tenant migration: $file\n";
    }
}

foreach ($masterMigrations as $file) {
    $path = 'database/migrations/' . $file;
    $content = file_get_contents($path);
    if (!str_contains($content, 'getConnection()->getName()')) {
        $content = preg_replace('/(public function up\(\)(?:\s*:\s*void)?\s*\{)/', "$1\n" . $masterCheck, $content);
        $content = preg_replace('/(public function down\(\)(?:\s*:\s*void)?\s*\{)/', "$1\n" . $masterCheck, $content);
        file_put_contents($path, $content);
        echo "Updated master migration: $file\n";
    }
}
