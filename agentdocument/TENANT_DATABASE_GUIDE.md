# TENANT_DATABASE_ARCHITECTURE & EXECUTION GUIDE

> **Agent Instruction:** Read this document BEFORE running any database query, artisan tinker command, or database operation. Do not re-analyze the codebase for multi-tenancy mechanics.

---

## 1. System Architecture (Multi-Database)

This codebase uses a **Separate Database per Tenant** architecture:
- **Master Database (`mysql` connection):** Database `workorio`. Contains `tenants`, `tenant_databases`, `migrations`, and master user accounts.
- **Tenant Databases (`tenant_{id}` connection):** E.g., `tenant_demo`, `tenant_acme`. Contains application data: `users`, `employees`, `tasks`, `sales_records`, `attendances`, etc.

---

## 2. Model Connections & Classes

| Model Class | Inherits From | Target DB |
|---|---|---|
| `Tenant`, `TenantDatabase` | `Illuminate\Database\Eloquent\Model` (hardcoded `$connection = 'mysql'`) | **Master DB** |
| `User`, `Employee`, `Task`, `SalesRecord`, `Attendance`, etc. | `TenantModel` (or tenant-aware Authenticatable) | **Tenant DB** |

---

## 3. How Tenant Switching Works at Runtime

1. **HTTP Web Requests:**
   - Handled automatically by `App\Http\Middleware\SetTenantDatabase`.
   - Identifies tenant via `X-Tenant-ID` header or `session('tenant_id')`.
   - Calls `TenantDatabaseService::setDefaultConnection($tenantId)`.

2. **Artisan / Console / Tinker (CRITICAL):**
   - **There is NO HTTP session or header.**
   - Default connection is always `mysql` (Master DB).
   - Attempting to query `User::all()` or `DB::table('users')` without switching will fail or read the master DB!

---

## 4. The Standard Execution Formula in Tinker / CLI

### Step 1: List Valid Tenants (Always do this first to avoid invalid ID errors)
```php
\App\Models\Tenant::select('id', 'tenant_name', 'tenant_code')->get();
```

### Step 2: Switch to the Target Tenant
```php
\App\Services\TenantDatabaseService::setDefaultConnection($tenantId);
```
*(Note: Returns `= null` in Tinker because return type is `void`. This indicates success).*

### Step 3: Verify the Connected Database
```php
DB::connection()->getDatabaseName();
```

### Step 4: Run Queries on the Tenant Database
```php
// Eloquent:
\App\Models\User::all();
\App\Models\Employee::count();

// Raw / Query Builder:
DB::select('SELECT id, name, email FROM users');
DB::table('tasks')->get();
```

### Step 5: Switch Back to Master (if needed)
```php
DB::setDefaultConnection('mysql');
```

---

## 5. Direct One-Liner Querying (Without changing default connection)

To query a tenant without altering the default connection state:
```php
$tenant = \App\Models\Tenant::find($tenantId);
\App\Services\TenantDatabaseService::createConnection($tenant);

// Run query on tenant connection directly:
DB::connection("tenant_{$tenantId}")->select('SELECT * FROM users');
\App\Models\User::on("tenant_{$tenantId}")->get();
```

---

## 6. How Tenant Database Credentials & Names are Resolved

Inside `App\Services\TenantDatabaseService`:
- Connection name formula: `"tenant_{$tenantId}"`
- Database name resolution:
  1. Checks `tenant_databases` table in Master DB (`where tenant_id = $tenantId`).
  2. If empty, falls back to: `env('TENANT_DB_PREFIX', '') . 'tenant_' . strtolower(tenant_code)`.
  3. Uses `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` from `.env` unless custom credentials exist in `tenant_databases`.
- Dynamic registration: Registers connection into Laravel runtime via `Config::set("database.connections.tenant_{$tenantId}", [...])`.
