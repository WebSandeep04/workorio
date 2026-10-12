# MIGRATION_STANDARDS_AND_RULES_FOR_AGENTS

> **Agent Instruction:** Read this document BEFORE creating or modifying ANY database migration. Strictly adhere to these boilerplate templates to ensure multi-database compatibility without trial, error, or wasted tokens.

---

## 1. Golden Architecture Rule

In this codebase, `php artisan migrate` or `php artisan migrate:fresh` executes across **BOTH** the Master database (`mysql`) and dynamically created Tenant databases (`tenant_{id}`).

- **Never create a migration without explicitly deciding its target scope:**
  1. **Tenant-Only Migration** (95% of business features)
  2. **Master-Only Migration** (System / SaaS subscription features)
  3. **Dual Migration** (Tables existing in both, like `users` or Laravel cache/jobs)

---

## 2. Decision Tree & Templates

### Category 1: Tenant-Only Migrations (Most Common)
> **Target:** Tables for business operations (e.g. `employees`, `attendance`, `sales_*`, `tasks`, `documents`, `shifts`, `whatsapp_*`, `places`, etc.)
> **Rule:** Must skip if the active connection is `'mysql'` (Master DB).

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if running on master database
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::create('your_tenant_table', function (Blueprint $table) {
            $table->id();
            // NEVER add tenant_id column! Tenant databases are already isolated.
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() === 'mysql') {
            return;
        }

        Schema::dropIfExists('your_tenant_table');
    }
};
```

---

### Category 2: Master-Only Migrations
> **Target:** System metadata (e.g. `tenants`, `tenant_databases`, centralized billing, SaaS plan features).
> **Rule:** Must skip if the active connection is NOT `'mysql'`.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip this migration if NOT running on master database
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            // Always wrap ALTER columns in hasColumn check for idempotency!
            if (!Schema::hasColumn('tenants', 'new_feature_flag')) {
                $table->boolean('new_feature_flag')->default(false);
            }
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getName() !== 'mysql') {
            return;
        }

        Schema::table('tenants', function (Blueprint $table) {
            if (Schema::hasColumn('tenants', 'new_feature_flag')) {
                $table->dropColumn('new_feature_flag');
            }
        });
    }
};
```

---

### Category 3: Dual Migrations (Both Master & Tenant)
> **Target:** Tables that exist on both databases (e.g. altering the dual `users` table or framework queue/cache tables).
> **Rule:** Do NOT check connection name, BUT ALWAYS wrap in `hasTable` and `hasColumn` checks to prevent `Duplicate column` or `Base table not found` exceptions.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No connection check, but defensive column checks are MANDATORY:
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'new_shared_field')) {
                    $table->string('new_shared_field')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'new_shared_field')) {
                    $table->dropColumn('new_shared_field');
                }
            });
        }
    }
};
```

---

## 3. Strict Prohibitions

1. ❌ **NEVER add `tenant_id` foreign keys to tenant tables:**
   Tenant databases are physically separate schemas (`tenant_1`, `tenant_2`). Adding `tenant_id` is redundant and breaks schema standards.
2. ❌ **NEVER add unguarded `Schema::table()` alterations to `tenants`:**
   Always check `if (!Schema::hasColumn('tenants', 'col'))` before `$table->boolean('col')`.
3. ❌ **NEVER alter a tenant-only table without `if (Schema::getConnection()->getName() === 'mysql') return;`:**
   If omitted, running `migrate:fresh` on master will fail with: `Base table or view not found: Table 'workorio.table_name' doesn't exist`.
