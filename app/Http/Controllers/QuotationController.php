<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;
use App\Models\Prospectus;
use App\Models\SalesProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Quotation;
use App\Models\QuotationRevision;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationController extends Controller
{
    /**
     * Display the quotation management page
     */
    public function index()
    {
        return view('quotation.index');
    }

    /**
     * List created quotations (generic, DB-driven)
     */
    public function list()
    {
        // Use current (tenant-aware) connection
        if (!Schema::hasTable('quotations')) {
            return response()->json(['data' => []]);
        }

        // Fetch latest 200 quotations and compute display name from source table
        $rows = DB::table('quotations as q')
            ->select(
                'q.*',
                DB::raw("CASE 
                    WHEN q.customer_type = 'customer' THEN (
                        SELECT CONCAT_WS(' ', c.name, CASE WHEN c.company_name IS NULL OR c.company_name = '' THEN '' ELSE CONCAT('(', c.company_name, ')') END)
                        FROM customers c WHERE c.id = q.customer_id
                    )
                    WHEN q.customer_type = 'prospect' THEN (
                        SELECT CONCAT_WS(' ', p.prospectus_name, CASE WHEN p.contact_person IS NULL OR p.contact_person = '' THEN '' ELSE CONCAT('(', p.contact_person, ')') END)
                        FROM prospectuses p WHERE p.id = q.customer_id
                    )
                    ELSE NULL END as customer_display_raw")
            )
            ->orderByDesc('q.id')
            ->limit(200)
            ->get();

        // Post-process to include manual name fallback from JSON data and file URL
        $rows = $rows->map(function ($r) {
            $display = $r->customer_display_raw;
            $r->customer_display = $display;
            unset($r->customer_display_raw);
            // Add file URL for download
            $r->file_url = $this->quoteFileUrl($r);
            return $r;
        });

        return response()->json(['data' => $rows]);
    }

    /**
     * Display the create quotation page
     */
    public function create()
    {
        return view('quotation.create');
    }

    /**
     * Get customers for quotation form
     */
    public function getCustomers()
    {
        $customers = Customer::select('id', 'name', 'email', 'phone', 'company_name')
            ->orderBy('name')
            ->get();
        
        return response()->json($customers);
    }

    /**
     * Get prospects for quotation form
     */
    public function getProspects()
    {
        $prospects = Prospectus::select('id', 'prospectus_name', 'contact_person', 'contact_number', 'email')
            ->orderBy('prospectus_name')
            ->get();
        
        return response()->json($prospects);
    }

    /**
     * Get sales products for quotation form
     */
    public function getSalesProducts()
    {
        $products = SalesProduct::select('id', 'product_name')
            ->orderBy('product_name')
            ->get();
        
        return response()->json($products);
    }

    /**
     * Get payment terms (best effort; supports missing or variant schema)
     */
    public function getPaymentTerms()
    {
        $fallback = [
            [
                'id' => 1,
                'name' => 'Standard (40/40/20)',
                'is_active' => 1,
                'advance_percentage' => 40,
                'design_dev_percentage' => 40,
                'completion_percentage' => 20,
            ],
        ];

        if (Schema::hasTable('payment_terms')) {
            $cols = DB::getSchemaBuilder()->getColumnListing('payment_terms');
            $nameCol = in_array('name', $cols) ? 'name' : (in_array('title', $cols) ? 'title' : 'id');
            $activeCol = in_array('is_active', $cols) ? 'is_active' : (in_array('active', $cols) ? 'active' : null);

            $sel = [DB::raw('id as id'), DB::raw($nameCol.' as name')];
            $sel[] = $activeCol ? DB::raw($activeCol.' as is_active') : DB::raw('1 as is_active');
            $sel[] = in_array('advance_percentage', $cols) ? DB::raw('advance_percentage') : DB::raw('0 as advance_percentage');
            $sel[] = in_array('design_dev_percentage', $cols) ? DB::raw('design_dev_percentage') : DB::raw('0 as design_dev_percentage');
            $sel[] = in_array('completion_percentage', $cols) ? DB::raw('completion_percentage') : DB::raw('0 as completion_percentage');

            $terms = DB::table('payment_terms')->select($sel)->orderBy('id')->get();

            if ($terms->isEmpty()) {
                return response()->json($fallback);
            }

            return response()->json($terms);
        }

        return response()->json($fallback);
    }

    /**
     * Generate quotation number
     */
    public function generateQuotationNumber()
    {
        $today = Carbon::now();
        $datePrefix = $today->format('Ymd');
        
        // Get the last quotation number for today
        $lastQuotation = \DB::table('quotations')
            ->where('quotation_number', 'like', "quote-{$datePrefix}%")
            ->orderBy('quotation_number', 'desc')
            ->first();
        
        if ($lastQuotation) {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        $quotationNumber = "quote-{$datePrefix}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        
        return response()->json(['quotation_number' => $quotationNumber]);
    }

    /**
     * Get current date
     */
    public function getCurrentDate()
    {
        return response()->json(['date' => Carbon::now()->format('Y-m-d')]);
    }

    /**
     * Latest quotation for an entity (customer or prospect)
     * Query params: type=customer|prospect, id=<entity_id>
     */
    public function latestForEntity(Request $request)
    {
        if (!Schema::hasTable('quotations')) {
            return response()->json(['data' => null]);
        }

        $type = $request->query('type');
        $id = (int) $request->query('id');
        if (!in_array($type, ['customer', 'prospect'], true) || $id <= 0) {
            return response()->json(['data' => null]);
        }

        $row = Quotation::where('customer_type', $type)
            ->where('customer_id', $id)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->first();

        if (!$row) {
            return response()->json(['data' => null]);
        }

        $fileUrl = $this->quoteFileUrl($row);
        return response()->json([
            'data' => [
                'id' => $row->id,
                'quotation_number' => $row->quotation_number,
                'version' => $row->version,
                'status' => $row->status,
                'total_amount' => $row->total_amount,
                'created_at' => $row->created_at,
                'file_url' => $fileUrl,
            ]
        ]);
    }
    /**
     * Show one quotation by its quotation_number (with revisions)
     */
    public function showByNumber(string $quotationNumber)
    {
        if (!Schema::hasTable('quotations')) {
            return response()->json(['error' => 'quotations table not found'], 404);
        }

        $q = Quotation::where('quotation_number', $quotationNumber)->first();
        if (!$q) {
            return response()->json(['error' => 'quotation not found'], 404);
        }

        $revisions = [];
        if (Schema::hasTable('quotation_revisions')) {
            $revisions = QuotationRevision::where('quotation_id', $q->id)
                ->orderByDesc('version')
                ->get();
        }

        return response()->json([
            'quotation' => $q,
            'revisions' => $revisions,
        ]);
    }

    /**
     * Return revision history for a quotation id (includes current live as latest).
     */
    public function revisions(int $id)
    {
        if (!Schema::hasTable('quotations')) {
            return response()->json(['data' => []]);
        }

        $q = Quotation::find($id);
        if (!$q) {
            return response()->json(['data' => []]);
        }

        $rows = [];
        // include current/live
        $rows[] = [
            'version'    => $q->version,
            'file_path'  => $q->file_path,
            'file_url'   => $this->quoteFileUrl($q),
            'created_at' => optional($q->updated_at ?? $q->created_at)->toDateTimeString(),
            'label'      => 'Current',
        ];

        if (Schema::hasTable('quotation_revisions')) {
            $revs = QuotationRevision::where('quotation_id', $q->id)
                ->orderByDesc('version')
                ->get()
                ->map(function ($r) {
                    return [
                        'version'    => $r->version,
                        'file_path'  => $r->file_path,
                        'file_url'   => $this->quoteFileUrl($r),
                        'created_at' => optional($r->created_at)->toDateTimeString(),
                        'label'      => 'Revision',
                    ];
                })->toArray();
            $rows = array_merge($rows, $revs);
        }

        // Sort by version desc
        usort($rows, function ($a, $b) {
            return ($b['version'] <=> $a['version']);
        });

        return response()->json(['data' => $rows, 'quotation' => $q]);
    }

    /**
     * Store quotation metadata + PDF, support revisions.
     * Expects JSON:
     *  - quotation_number (optional; if missing, one can be generated client-side via generateQuotationNumber)
     *  - customer_type: 'customer'|'prospect'
     *  - customer_id (nullable)
     *  - payment_term_id (nullable)
     *  - project_timeline (nullable)
     *  - products: array
     *  - total_amount (number)
     *  - pdf_base64 (obsolete/ignored)
     */
    public function store(Request $request)
    {
        Log::info('Quotation store called', [
            'connection' => DB::connection()->getDatabaseName(),
            'has_session' => session()->has('user_id'),
            'session_tenant' => session('tenant_id'),
        ]);

        $userId = $this->getCurrentUserId();
        if (!$userId) {
            Log::error('Quotation store: No user ID found');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Log::info('Quotation store: User ID found', ['user_id' => $userId]);

        try {
            $data = $request->validate([
            'quotation_number'   => 'nullable|string|max:190',
            'customer_type'      => 'required|in:customer,prospect',
            'customer_id'        => 'nullable|integer',
            'payment_term_id'    => 'nullable|integer',
            'project_timeline'   => 'nullable|string|max:255',
            'products'           => 'array',
            'products.*.product_id' => 'required',
            'products.*.price'      => 'required|numeric',
            'products.*.quantity'   => 'nullable|numeric|min:0.01',
            'products.*.unit'       => 'nullable|string|max:50',
            'products.*.remark'     => 'nullable|string',
            'discount'           => 'nullable|numeric|min:0',
            'total_amount'       => 'nullable|numeric',
            'status'             => 'nullable|string|max:100',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Quotation validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        Log::info('Quotation validation passed', ['data_keys' => array_keys($data)]);

        // Ensure we have a quotation number
        $quoteNo = $data['quotation_number'] ?? null;
        if (!$quoteNo) {
            // fallback to generated number
            $today = Carbon::now();
            $datePrefix = $today->format('Ymd');
            $last = DB::table('quotations')->where('quotation_number', 'like', "quote-{$datePrefix}%")->orderByDesc('quotation_number')->first();
            if ($last) {
                $n = (int) substr($last->quotation_number, -3);
                $quoteNo = "quote-{$datePrefix}-" . str_pad($n + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $quoteNo = "quote-{$datePrefix}-001";
            }
        }

        // Generate PDF Binary using the selected template
        $binary = $this->generatePdfBinary($data, $quoteNo);
        if (!$binary || (is_array($binary) && isset($binary['error']))) {
            $errMsg = is_array($binary) ? $binary['error'] : 'Unknown error';
            Log::error('Quotation store: Failed to generate PDF binary', ['error' => $errMsg]);
            return response()->json([
                'message' => 'PDF generation failed',
                'details' => $errMsg
            ], 500);
        }

        Log::info('Starting quotation save transaction', ['quote_no' => $quoteNo]);
        Log::info('Quotation transaction started', [
            'quotation_number' => $quoteNo,
            'database' => DB::connection()->getDatabaseName(),
            'user_id' => $userId
        ]);

        try {
            return DB::transaction(function () use ($data, $binary, $quoteNo, $userId) {
            $existing = Quotation::where('quotation_number', $quoteNo)->lockForUpdate()->first();
            $nextVersion = $existing ? ($existing->version + 1) : 1;

            Log::info('Quotation version check', [
                'existing' => $existing ? 'yes' : 'no',
                'next_version' => $nextVersion
            ]);

            // Compute file path
            $dir = 'quotations/' . $quoteNo;
            $filename = $quoteNo . '_v' . $nextVersion . '.pdf';
            $path = $dir . '/' . $filename;
            
            try {
                Storage::disk('public')->put($path, $binary);
                Log::info('PDF file saved successfully', ['path' => $path]);
            } catch (\Exception $e) {
                Log::error('Failed to save PDF file', ['error' => $e->getMessage()]);
                throw $e;
            }

            if ($existing) {
                // Save previous as revision
                try {
                    QuotationRevision::create([
                        'quotation_id' => $existing->id,
                        'version'      => $existing->version,
                        'file_path'    => $existing->file_path ?? '',
                        'data'         => $existing->data,
                        'created_by'   => $userId,
                    ]);
                    Log::info('Quotation revision created successfully', [
                        'quotation_id' => $existing->id,
                        'version' => $existing->version
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to create quotation revision', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }

                $existing->fill([
                    'customer_type'    => $data['customer_type'],
                    'customer_id'      => $data['customer_id'] ?? null,
                    'payment_term_id'  => $data['payment_term_id'] ?? null,
                    'project_timeline' => $data['project_timeline'] ?? null,
                    'total_amount'     => $data['total_amount'] ?? 0,
                    'status'           => $data['status'] ?? 'Draft',
                    'version'          => $nextVersion,
                    'file_path'        => $path,
                    'data'             => [
                        'products' => $data['products'] ?? [],
                        'discount' => $data['discount'] ?? 0,
                    ],
                    'updated_by'       => $userId,
                ])->save();

                Log::info('Quotation updated successfully', [
                    'id' => $existing->id,
                    'quotation_number' => $existing->quotation_number,
                    'version' => $nextVersion
                ]);

                $payload = $existing->fresh()->toArray();
                $payload['file_url'] = $this->quoteFileUrl($existing);
                $payload['revised']   = true;
                return response()->json(['message' => 'Quotation revised', 'data' => $payload]);
            }

            try {
                $quote = Quotation::create([
                'quotation_number'   => $quoteNo,
                'customer_type'      => $data['customer_type'],
                'customer_id'        => $data['customer_id'] ?? null,
                'payment_term_id'    => $data['payment_term_id'] ?? null,
                'project_timeline'   => $data['project_timeline'] ?? null,
                'total_amount'       => $data['total_amount'] ?? 0,
                'status'             => $data['status'] ?? 'Draft',
                'version'            => $nextVersion,
                'file_path'          => $path,
                'data'               => [
                    'products' => $data['products'] ?? [],
                    'discount' => $data['discount'] ?? 0,
                ],
                'created_by'         => $userId,
                'updated_by'         => $userId,
                ]);

                Log::info('Quotation created successfully', [
                    'id' => $quote->id,
                    'quotation_number' => $quote->quotation_number
                ]);

                $payload = $quote->toArray();
                $payload['file_url'] = $this->quoteFileUrl($quote);
                $payload['revised']   = false;
                return response()->json(['message' => 'Quotation saved', 'data' => $payload]);
            } catch (\Exception $e) {
                Log::error('Failed to create quotation', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
        } catch (\Exception $e) {
            Log::error('Quotation store transaction failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw $e;
        }
    }

    /**
     * Download a stored quotation PDF securely.
     */
    public function download(Quotation $quotation)
    {
        if (!$quotation->file_path || !Storage::disk('public')->exists($quotation->file_path)) {
            abort(404, 'Quotation PDF not found');
        }

        $downloadName = basename($quotation->file_path);
        return Storage::disk('public')->download($quotation->file_path, $downloadName);
    }

    /**
     * Get current user ID from Auth or session
     */
    private function getCurrentUserId()
    {
        // Check Laravel Auth first (for super admin)
        if (Auth::check()) {
            return Auth::id();
        }

        // Check session for tenant users
        if (session()->has('user_id')) {
            return session('user_id');
        }

        return null;
    }

    /**
     * Build a secure download URL for a quotation's stored PDF.
     */
    private function quoteFileUrl($quote)
    {
        if (!$quote || empty($quote->file_path)) {
            return null;
        }

        // Use direct storage URL to avoid route model binding issues with tenant databases
        return Storage::disk('public')->url($quote->file_path);
    }

    /**
     * Generate PDF binary using chosen template
     */
    private function generatePdfBinary($data, $quoteNumber)
    {
        $settings = DB::table('quotation_settings')->first();
        if (!$settings) {
            $settings = (object) [
                'template_name' => 'modern',
                'primary_color' => '#434AFA',
                'secondary_color' => '#FF8C00'
            ];
        }

        // Create a temporary quotation object for the view
        $quote = new Quotation();
        $quote->quotation_number = $quoteNumber;
        $quote->customer_type = $data['customer_type'];
        $quote->customer_id = $data['customer_id'];
        $quote->total_amount = $data['total_amount'] ?? 0;
        $quote->created_at = now();
        // Fetch product names for display
        $products = $data['products'] ?? [];
        foreach ($products as &$p) {
            if (!isset($p['product_name']) && isset($p['product_id'])) {
                $product = DB::table('sales_products')->where('id', $p['product_id'])->first();
                if ($product) {
                    $p['product_name'] = $product->product_name;
                }
            }
        }

        $quote->data = [
            'products' => $products,
            'discount' => $data['discount'] ?? 0,
            'project_timeline' => $data['project_timeline'] ?? ''
        ];

        // Eager load customer if exists
        if ($quote->customer_type == 'customer') {
            $quote->setRelation('customer', Customer::find($quote->customer_id));
        }

        $template = $settings->template_name ?? 'modern';
        $viewPath = "quotation.templates.{$template}";

        if (!view()->exists($viewPath)) {
            $viewPath = 'quotation.templates.modern';
        }

        // Prepare logo as base64 for better compatibility in PDFs
        $logoBase64 = null;
        if (isset($settings->logo_path) && $settings->logo_path) {
            try {
                if (Storage::disk('public')->exists($settings->logo_path)) {
                    $logoData = Storage::disk('public')->get($settings->logo_path);
                    $logoType = Storage::disk('public')->mimeType($settings->logo_path);
                    $logoBase64 = 'data:' . $logoType . ';base64,' . base64_encode($logoData);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to convert logo to base64: ' . $e->getMessage());
            }
        }

        try {
            // Increase memory limit for PDF generation
            ini_set('memory_limit', '512M');
            
            // Ensure font cache directory exists
            $fontDir = storage_path('fonts');
            if (!file_exists($fontDir)) {
                @mkdir($fontDir, 0755, true);
            }

            $pdf = Pdf::loadView($viewPath, [
                'quote' => $quote,
                'settings' => $settings,
                'logo_base64' => $logoBase64
            ]);

            return $pdf->output();
        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            // Re-throw if we want the outer store method to catch more details, 
            // but for now let's just make sure the message is logged.
            return ['error' => $e->getMessage()];
        }
    }
}
