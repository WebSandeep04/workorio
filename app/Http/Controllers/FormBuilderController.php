<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FormBuilder;
// use App\Models\FormBuilderSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class FormBuilderController extends Controller
{
    /**
     * Display the Form Builder (Lead Form) index page - list all forms.
     */
    public function index()
    {
        if (!Schema::hasTable('form_builders')) {
            $forms = collect();
            return view('formbuilder.index', compact('forms'))
                ->with('needs_migration', true);
        }
        $forms = FormBuilder::orderByDesc('updated_at')->paginate(20);
        return view('formbuilder.index', compact('forms'));
    }

    /**
     * Show the form creation page.
     */
    public function create()
    {
        if (!Schema::hasTable('form_builders')) {
            return redirect()->route('formbuilder.index');
        }
        return view('formbuilder.form');
    }

    /**
     * Show the form editing page.
     */
    public function edit($form)
    {
        if (!Schema::hasTable('form_builders')) {
            return redirect()->route('formbuilder.index');
        }
        $row = FormBuilder::findOrFail($form);
        return view('formbuilder.form', ['form' => $row]);
    }

    /**
     * Return column metadata for the indiamartleads table from the current (tenant) DB.
     */
    public function fields(Request $request)
    {
        $connection = \DB::connection();
        $database = $connection->getDatabaseName();

        $table = 'indiamartleads';

        try {
            $rows = \DB::select(
                "SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, COLUMN_COMMENT
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
                [$database, $table]
            );

            $columns = [];
            // allow only sender-related fields and a few extras for the lead form builder
            $allowedExact = [
                'company_name',
                'no_of_employees',
                'remarks',
            ];
            $allowedPrefixes = ['sender_'];

            foreach ($rows as $r) {
                $name = $r->COLUMN_NAME;
                $isSender = false;
                foreach ($allowedPrefixes as $p) {
                    if (strpos($name, $p) === 0) { $isSender = true; break; }
                }
                if (!$isSender && !in_array($name, $allowedExact, true)) {
                    continue; // skip non-sender, non-whitelisted columns
                }

                $isNullable = strtoupper($r->IS_NULLABLE ?? 'YES') === 'YES';
                $dataType = strtolower($r->DATA_TYPE ?? 'varchar');
                $default = $r->COLUMN_DEFAULT;
                $isId = in_array($name, ['id']);
                $required = !$isNullable && !$isId;

                $columns[] = [
                    'name'      => $name,
                    'data_type' => $dataType,
                    'nullable'  => $isNullable,
                    'required'  => $required,
                    'key'       => $r->COLUMN_KEY,
                    'default'   => $default,
                    'comment'   => $r->COLUMN_COMMENT,
                ];
            }

            return response()->json([
                'table'   => $table,
                'columns' => $columns,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Persist a new form (name + selected fields)
     */
    public function store(Request $request)
    {
        if (!Schema::hasTable('form_builders')) {
            return response()->json(['success' => false, 'message' => 'Table form_builders not found. Run migrations.'], 500);
        }
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'nullable|string',
            'fields.*.label' => 'nullable|string',
            'fields.*.required' => 'nullable|boolean',
        ]);

        $userId = session('user_id') ?: (auth()->check() ? auth()->id() : null);

        $form = FormBuilder::create([
            'name' => $data['name'],
            'fields' => $data['fields'],
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);

        return response()->json(['id' => $form->id, 'success' => true]);
    }

    /**
     * List saved forms (id, name, updated_at)
     */
    public function list()
    {
        if (!Schema::hasTable('form_builders')) {
            return response()->json(['data' => []]);
        }
        $rows = FormBuilder::orderByDesc('updated_at')->get(['id','name','updated_at']);
        return response()->json(['data' => $rows]);
    }

    /**
     * Show one form (for editing)
     */
    public function show($form)
    {
        if (!Schema::hasTable('form_builders')) {
            return response()->json(['error' => 'Table form_builders not found.'], 404);
        }
        $row = FormBuilder::findOrFail($form);
        return response()->json(['data' => $row]);
    }

    /**
     * Render a saved form (read-only page for viewing the form layout)
     */
    public function viewPage(Request $request, $form)
    {
        if (!Schema::hasTable('form_builders')) {
            return redirect()->route('formbuilder.index');
        }
        $row = FormBuilder::findOrFail($form);
        if ($request->boolean('embed')) {
            return view('formbuilder.embed', ['form' => $row]);
        }
        return view('formbuilder.show', ['form' => $row]);
    }

    /**
     * Show database configuration page for a form
     */
    public function config($form)
    {
        if (!Schema::hasTable('form_builders')) {
            return redirect()->route('formbuilder.index');
        }
        $row = FormBuilder::findOrFail($form);
        return view('formbuilder.config', ['form' => $row]);
    }

    /**
     * Save database configuration for a form
     */
    public function saveConfig(Request $request, $form)
    {
        if (!Schema::hasTable('form_builders')) {
            return redirect()->route('formbuilder.index');
        }

        $data = $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'nullable|string|max:10',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
            'db_database' => 'required|string|max:255',
        ]);

        $row = FormBuilder::findOrFail($form);
        $row->update([
            'db_host' => $data['db_host'],
            'db_port' => $data['db_port'] ?? '3306',
            'db_username' => $data['db_username'],
            'db_password' => $data['db_password'] ?? '',
            'db_database' => $data['db_database'],
        ]);

        return redirect()->route('formbuilder.config', $row->id)->with('success', 'Database configuration saved successfully!');
    }

    /**
     * Test database connection
     */
    public function testConnection(Request $request, $form)
    {
        $data = $request->validate([
            'host' => 'required|string',
            'port' => 'nullable|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
            'database' => 'required|string',
        ]);

        try {
            $connName = 'test_form_' . $form . '_' . time();
            \Config::set("database.connections.{$connName}", [
                'driver' => 'mysql',
                'host' => $data['host'],
                'port' => $data['port'] ?? '3306',
                'database' => $data['database'],
                'username' => $data['username'],
                'password' => $data['password'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);

            $connection = DB::connection($connName);
            $connection->getPdo(); // Test connection

            // Check if indiamartleads table exists
            if (!Schema::connection($connName)->hasTable('indiamartleads')) {
                return response()->json(['success' => false, 'message' => 'Connection successful but indiamartleads table not found in this database.']);
            }

            return response()->json(['success' => true, 'message' => 'Connection successful and indiamartleads table found.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Accept public form submission and store JSON payload
     */
    public function submit(Request $request, $form)
    {
        if (!Schema::hasTable('form_builders')) {
            return redirect()->back()->with('error', 'Form not available.');
        }
        $row = FormBuilder::findOrFail($form);
        $fields = is_array($row->fields) ? $row->fields : (json_decode($row->fields, true) ?: []);

        // Build validation dynamically
        $debugId = (string) Str::uuid();
        Log::info('[FormSubmit]['.$debugId.'] Incoming submit', [
            'form_id' => $row->id,
            'ip' => $request->ip(),
            'ua' => $request->userAgent(),
        ]);
        $rules = [];
        foreach ($fields as $f) {
            $name = $f['name'] ?? null;
            if (!$name) { continue; }
            $type = strtolower($f['type'] ?? ($f['data_type'] ?? 'text'));
            $isRequired = (bool)($f['required'] ?? false);
            $base = $isRequired ? 'required' : 'nullable';
            if (str_contains($name, 'email')) {
                $rules[$name] = $base . '|email|max:255';
            } elseif (in_array($type, ['int','bigint','integer','smallint','mediumint','tinyint','decimal','double','float'])) {
                $rules[$name] = $base . '|string|max:255'; // keep flexible; can be numeric if needed
            } elseif (in_array($type, ['timestamp','datetime','date'])) {
                $rules[$name] = $base . '|string|max:255';
            } elseif (str_contains($type, 'text')) {
                $rules[$name] = $base . '|string';
            } else {
                $rules[$name] = $base . '|string|max:255';
            }
        }
        Log::info('[FormSubmit]['.$debugId.'] Built rules', ['rules' => $rules]);

        $validated = $request->validate($rules);
        Log::info('[FormSubmit]['.$debugId.'] Validated input', ['validated' => $validated]);

        // Use configured database connection if available, otherwise use default
        $connection = null;
        $database = null;
        
        if ($row->db_host && $row->db_database && $row->db_username) {
            // Create a temporary connection for this form
            $connName = 'form_builder_' . $row->id;
            \Config::set("database.connections.{$connName}", [
                'driver' => 'mysql',
                'host' => $row->db_host,
                'port' => $row->db_port ?? '3306',
                'database' => $row->db_database,
                'username' => $row->db_username,
                'password' => $row->db_password ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);
            $connection = DB::connection($connName);
            $database = $row->db_database;
            Log::info('[FormSubmit]['.$debugId.'] Using configured connection', [
                'conn' => $connName,
                'host' => $row->db_host,
                'port' => $row->db_port,
                'database' => $database,
                'username' => $row->db_username,
            ]);
        } else {
            // Fallback to default connection
            $connection = DB::connection();
            $database = $connection->getDatabaseName();
            Log::info('[FormSubmit]['.$debugId.'] Using default connection', [
                'conn' => $connection->getName(),
                'database' => $database,
            ]);
        }

        // Insert into indiamartleads table (only whitelisted columns)
        if (!Schema::connection($connection->getName())->hasTable('indiamartleads')) {
            Log::error('[FormSubmit]['.$debugId.'] Table indiamartleads not found on connection', [
                'conn' => $connection->getName(),
                'database' => $database,
            ]);
            return redirect()->back()->with('error', 'Target table indiamartleads not found in the configured database.');
        }

        $table = 'indiamartleads';

        $rows = $connection->select(
            "SELECT COLUMN_NAME, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
            [$database, $table]
        );
        $existingColumns = [];
        foreach ($rows as $r) { $existingColumns[$r->COLUMN_NAME] = strtoupper($r->IS_NULLABLE ?? 'YES') === 'YES'; }
        Log::info('[FormSubmit]['.$debugId.'] Found columns', ['columns' => $existingColumns]);

        // Allow only sender-related fields and company_name per earlier whitelist
        $allowedExact = ['company_name', 'no_of_employees', 'remarks'];
        $allowedPrefixes = ['sender_'];
        $payload = [];
        foreach ($validated as $key => $value) {
            $isAllowed = in_array($key, $allowedExact, true);
            if (!$isAllowed) {
                foreach ($allowedPrefixes as $p) { if (strpos($key, $p) === 0) { $isAllowed = true; break; } }
            }
            if (!$isAllowed) { continue; }
            if (!array_key_exists($key, $existingColumns)) { continue; }
            $payload[$key] = $value;
        }
        Log::info('[FormSubmit]['.$debugId.'] Payload after whitelist', ['payload' => $payload]);

        // Add required system fields if present
        // unique_query_id
        if (array_key_exists('unique_query_id', $existingColumns) && !isset($payload['unique_query_id'])) {
            // If column is NOT NULL (nullable=false), ensure a value
            $isNullable = (bool)($existingColumns['unique_query_id']);
            if (!$isNullable) {
                $payload['unique_query_id'] = (string) Str::uuid();
            }
        }

        // Timestamps if table uses them
        if (array_key_exists('created_at', $existingColumns) && !isset($payload['created_at'])) {
            $payload['created_at'] = now();
        }
        if (array_key_exists('updated_at', $existingColumns) && !isset($payload['updated_at'])) {
            $payload['updated_at'] = now();
        }

        if (empty($payload)) {
            Log::warning('[FormSubmit]['.$debugId.'] Empty payload after filtering');
            return redirect()->back()->with('error', 'No valid fields to save.');
        }

        try {
            $connection->table($table)->insert($payload);
            Log::info('[FormSubmit]['.$debugId.'] Inserted successfully', ['table' => $table]);
        } catch (\Throwable $e) {
            Log::error('[FormSubmit]['.$debugId.'] Insert failed', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return redirect()->back()->with('error', 'DB insert failed: ' . $e->getMessage());
        }

        $redirectRoute = ['formbuilder.view', $row->id];
        if ($request->boolean('_fb_embed')) {
            $redirectRoute = ['formbuilder.view', ['form' => $row->id, 'embed' => 1]];
        }

        return redirect()->route(...$redirectRoute)->with('success', 'Thanks! Your response has been recorded.');
    }

    /**
     * Update a saved form
     */
    public function update(Request $request, $form)
    {
        if (!Schema::hasTable('form_builders')) {
            return response()->json(['success' => false, 'message' => 'Table form_builders not found. Run migrations.'], 500);
        }
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string',
            'fields.*.type' => 'nullable|string',
            'fields.*.label' => 'nullable|string',
            'fields.*.required' => 'nullable|boolean',
        ]);

        $userId = session('user_id') ?: (auth()->check() ? auth()->id() : null);

        $row = FormBuilder::findOrFail($form);
        $row->update([
            'name' => $data['name'],
            'fields' => $data['fields'],
            'updated_by' => $userId,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a saved form
     */
    public function destroy($form)
    {
        if (!Schema::hasTable('form_builders')) {
            return response()->json(['success' => false, 'message' => 'Table form_builders not found.'], 404);
        }
        $row = FormBuilder::findOrFail($form);
        $row->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Public access to view a form (for iframe embedding on external sites)
     */
    public function publicView(Request $request, $tenant, $form)
    {
        // 1. Switch to tenant database
        try {
            \App\Services\TenantDatabaseService::setDefaultConnection($tenant);
        } catch (\Exception $e) {
            abort(404, 'Tenant not found or database inaccessible.');
        }

        if (!Schema::hasTable('form_builders')) {
            abort(404, 'Form Builder not active for this tenant.');
        }

        $row = FormBuilder::findOrFail($form);
        
        // Pass a custom action URL to the view, so the form posts to the public submit route
        $submitUrl = route('public.form.submit', ['tenant' => $tenant, 'form' => $form]);

        return view('formbuilder.embed', [
            'form' => $row,
            'publicMode' => true,
            'tenantId' => $tenant,
            'submitUrl' => $submitUrl
        ]);
    }

    /**
     * Public submit handler
     */
    public function publicSubmit(Request $request, $tenant, $form)
    {
        // 1. Switch to tenant database
        try {
            \App\Services\TenantDatabaseService::setDefaultConnection($tenant);
        } catch (\Exception $e) {
             return response()->json(['success'=>false, 'message'=>'Tenant DB error'], 500);
        }

        if (!Schema::hasTable('form_builders')) {
            return redirect()->back()->with('error', 'Form not available.');
        }

        $row = FormBuilder::findOrFail($form);
        $fields = is_array($row->fields) ? $row->fields : (json_decode($row->fields, true) ?: []);

        // Validation Logic (Duplicate of submit)
        $rules = [];
        foreach ($fields as $f) {
            $name = $f['name'] ?? null;
            if (!$name) { continue; }
            $type = strtolower($f['type'] ?? ($f['data_type'] ?? 'text'));
            $isRequired = (bool)($f['required'] ?? false);
            $base = $isRequired ? 'required' : 'nullable';
            if (str_contains($name, 'email')) {
                $rules[$name] = $base . '|email|max:255';
            } elseif (in_array($type, ['int','bigint','integer','smallint','mediumint','tinyint','decimal','double','float'])) {
                $rules[$name] = $base . '|string|max:255'; 
            } elseif (in_array($type, ['timestamp','datetime','date'])) {
                $rules[$name] = $base . '|string|max:255';
            } elseif (str_contains($type, 'text')) {
                $rules[$name] = $base . '|string';
            } else {
                $rules[$name] = $base . '|string|max:255';
            }
        }

        $validated = $request->validate($rules);

        // Database Connection Logic
        $connection = null;
        $database = null;
        
        if ($row->db_host && $row->db_database && $row->db_username) {
            $connName = 'form_builder_' . $row->id;
            \Config::set("database.connections.{$connName}", [
                'driver' => 'mysql',
                'host' => $row->db_host,
                'port' => $row->db_port ?? '3306',
                'database' => $row->db_database,
                'username' => $row->db_username,
                'password' => $row->db_password ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]);
            $connection = DB::connection($connName);
            $database = $row->db_database;
        } else {
            $connection = DB::connection();
            $database = $connection->getDatabaseName();
        }

        // Insert Logic
        if (!Schema::connection($connection->getName())->hasTable('indiamartleads')) {
            return redirect()->back()->with('error', 'Target table indiamartleads not found in the configured database.');
        }

        $table = 'indiamartleads';
        $rows = $connection->select(
            "SELECT COLUMN_NAME, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
            [$database, $table]
        );
        $existingColumns = [];
        foreach ($rows as $r) { $existingColumns[$r->COLUMN_NAME] = strtoupper($r->IS_NULLABLE ?? 'YES') === 'YES'; }

        $allowedExact = ['company_name', 'no_of_employees', 'remarks'];
        $allowedPrefixes = ['sender_'];
        $payload = [];
        foreach ($validated as $key => $value) {
            $isAllowed = in_array($key, $allowedExact, true);
            if (!$isAllowed) {
                foreach ($allowedPrefixes as $p) { if (strpos($key, $p) === 0) { $isAllowed = true; break; } }
            }
            if (!$isAllowed) { continue; }
            if (!array_key_exists($key, $existingColumns)) { continue; }
            $payload[$key] = $value;
        }

        if (array_key_exists('unique_query_id', $existingColumns) && !isset($payload['unique_query_id'])) {
            $isNullable = (bool)($existingColumns['unique_query_id']);
            if (!$isNullable) {
                $payload['unique_query_id'] = (string) Str::uuid();
            }
        }
        if (array_key_exists('created_at', $existingColumns) && !isset($payload['created_at'])) {
            $payload['created_at'] = now();
        }
        if (array_key_exists('updated_at', $existingColumns) && !isset($payload['updated_at'])) {
            $payload['updated_at'] = now();
        }

        if (!empty($payload)) {
            try {
                $connection->table($table)->insert($payload);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'DB insert failed: ' . $e->getMessage());
            }
        } else {
             return redirect()->back()->with('error', 'No valid fields to save.');
        }

        return redirect()->route('public.form.view', ['tenant' => $tenant, 'form' => $form])
                         ->with('success', 'Thanks! Your response has been recorded.');
    }
}


