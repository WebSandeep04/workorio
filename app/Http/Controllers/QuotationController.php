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

        // Paginate results (default 10 per page)
        $query = DB::table('quotations as q')
            ->leftJoin('users as u', 'q.created_by', '=', 'u.id')
            ->select(
                'q.*',
                'u.name as creator_name',
                DB::raw("CASE 
                    WHEN q.customer_type = 'customer' THEN (
                        SELECT CONCAT_WS(' ', c.name, CASE WHEN c.company_name IS NULL OR c.company_name = '' THEN '' ELSE CONCAT('(', c.company_name, ')') END)
                        FROM customers c WHERE c.id = q.customer_id
                    )
                    WHEN q.customer_type = 'prospect' THEN (
                        SELECT CONCAT_WS(' ', p.prospectus_name, CASE WHEN p.contact_person IS NULL OR p.contact_person = '' THEN '' ELSE CONCAT('(', p.contact_person, ')') END)
                        FROM prospectuses p WHERE p.id = COALESCE(q.prospect_id, q.customer_id)
                    )
                    ELSE NULL END as customer_display_raw")
            );

        // Apply Filters
        if ($userId = request('created_by')) {
            $query->where('q.created_by', $userId);
        }
        if ($custId = request('customer_id')) {
            $query->where('q.customer_type', 'customer')->where('q.customer_id', $custId);
        }
        if ($prosId = request('prospect_id')) {
            $query->where('q.customer_type', 'prospect')->where(function($qq) use ($prosId) {
                $qq->where('q.prospect_id', $prosId)->orWhere('q.customer_id', $prosId);
            });
        }
        if ($type = request('customer_type')) {
            $query->where('q.customer_type', $type);
        }
        if ($from = request('from_date')) {
            $query->whereDate('q.created_at', '>=', $from);
        }
        if ($to = request('to_date')) {
            $query->whereDate('q.created_at', '<=', $to);
        }
        if ($search = request('search')) {
            $query->where(function($qq) use ($search) {
                $qq->where('q.quotation_number', 'like', "%{$search}%")
                   ->orWhereExists(function($sq) use ($search) {
                       $sq->select(DB::raw(1))
                          ->from('customers as c2')
                          ->whereColumn('c2.id', 'q.customer_id')
                          ->where('q.customer_type', 'customer')
                          ->where('c2.name', 'like', "%{$search}%");
                   })
                   ->orWhereExists(function($sq) use ($search) {
                       $sq->select(DB::raw(1))
                          ->from('prospectuses as p2')
                          ->where(function($p2q) {
                              $p2q->whereColumn('p2.id', 'q.prospect_id')
                                  ->orWhereColumn('p2.id', 'q.customer_id');
                          })
                          ->where('q.customer_type', 'prospect')
                          ->where('p2.prospectus_name', 'like', "%{$search}%");
                   });
            });
        }

        $rows = $query->orderByDesc('q.id')->paginate(request('per_page', 10));

        // Post-process the collection inside the paginator
        $rows->getCollection()->transform(function ($r) {
            $display = $r->customer_display_raw;

            if (empty($display)) {
                if ($r->customer_type == 'customer' && !empty($r->customer_id)) {
                    $c = DB::table('customers')->find($r->customer_id);
                    if ($c) {
                        $display = $c->name . ($c->company_name ? " ({$c->company_name})" : "");
                    }
                } elseif ($r->customer_type == 'prospect') {
                    $pId = $r->prospect_id ?? $r->customer_id;
                    if (!empty($pId)) {
                        $p = DB::table('prospectuses')->find($pId);
                        if ($p) {
                            $display = $p->prospectus_name . ($p->contact_person ? " ({$p->contact_person})" : "");
                        }
                    }
                }
            }

            $r->customer_display = $display ?: '-';
            unset($r->customer_display_raw);
            $r->file_url = $this->quoteFileUrl($r);
            return $r;
        });

        return response()->json($rows);
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
     * Get users for quotation filter
     */
    public function getUsers()
    {
        $users = DB::table('users')->select('id', 'name')->orderBy('name')->get();
        return response()->json($users);
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
        $type = request('customer_type');
        $id = request('customer_id');
        $quotationNumber = $this->calculateNextQuotationNumber($type, $id);
        return response()->json(['quotation_number' => $quotationNumber]);
    }

    /**
     * Unified logic for calculating the next quotation number based on client prefix.
     */
    private function calculateNextQuotationNumber($type, $id)
    {
        $today = Carbon::now();
        $datePrefix = $today->format('Ymd');
        $compPrefix = 'TRIS'; // Default

        if ($type && $id) {
            $name = '';
            if ($type === 'customer') {
                $customer = \App\Models\Customer::find($id);
                $name = $customer->company_name ?? '';
            } else if ($type === 'prospect') {
                $prospect = \App\Models\Prospectus::find($id);
                $name = $prospect->prospectus_name ?? '';
            }

            if (!empty(trim($name))) {
                // Remove spaces and special characters for a clean prefix
                $cleanName = preg_replace('/[^A-Za-z0-9]/', '', $name);
                $compPrefix = strtoupper(substr($cleanName, 0, 4));
            }
        }

        if (empty($compPrefix)) {
            $compPrefix = 'TRIS';
        }

        // Get the last quotation number for today (check for both old 'quote-' and new prefix)
        $lastQuotation = DB::table('quotations')
            ->where(function($query) use ($datePrefix, $compPrefix) {
                $query->where('quotation_number', 'like', "quote-{$datePrefix}%")
                      ->orWhere('quotation_number', 'like', "{$compPrefix}-{$datePrefix}%");
            })
            ->orderBy('quotation_number', 'desc')
            ->first();
        
        $newNumber = 1;
        if ($lastQuotation) {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -3);
            $newNumber = $lastNumber + 1;
        }
        
        return "{$compPrefix}-{$datePrefix}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
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

        // Load related customer or prospect relationship
        $q->loadMissing(['customer', 'prospect']);

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
            'file_url'   => $this->quoteFileUrl($q, 'current'),
            'created_at' => optional($q->updated_at ?? $q->created_at)->toDateTimeString(),
            'label'      => 'Current',
        ];

        if (Schema::hasTable('quotation_revisions')) {
            $revs = QuotationRevision::where('quotation_id', $q->id)
                ->orderByDesc('version')
                ->get()
                ->map(function ($r) {
                    return [
                        'id'         => $r->id,
                        'version'    => $r->version,
                        'file_path'  => $r->file_path,
                        'file_url'   => $this->quoteFileUrl($r, 'revision'),
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
            'subject'            => 'nullable|string|max:255',
            'products'           => 'array',
            'products.*.product_id' => 'required',
            'products.*.price'      => 'required|numeric',
            'products.*.quantity'   => 'nullable|numeric|min:0',
            'products.*.unit'       => 'nullable|string|max:50',
            'products.*.remark'     => 'nullable|string',
            'products.*.discount'   => 'nullable|numeric|min:0',
            'products.*.discount_type' => 'nullable|string|in:fixed,percentage',
            'discount'           => 'nullable|numeric|min:0',
            'total_amount'       => 'nullable|numeric',
            'global_discount_type' => 'nullable|string|in:fixed,percentage',
            'status'             => 'nullable|string|max:100',
            'payment_term_id'    => 'nullable|integer',
            'project_timeline'   => 'nullable|string|max:255',
            'payment_terms'      => 'nullable|string',
            'show_payment_terms' => 'nullable|boolean', // Removed 'string' to avoid conflict with boolean type
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Quotation validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        Log::info('Quotation validation passed', ['data_keys' => array_keys($data)]);

        // Ensure we have a quotation number
        $quoteNo = $data['quotation_number'] ?? null;
        if (!$quoteNo) {
            $quoteNo = $this->calculateNextQuotationNumber($data['customer_type'], $data['customer_id']);
        }

        Log::info('Starting quotation save transaction', ['quote_no' => $quoteNo]);

        try {
            return DB::transaction(function () use ($data, $quoteNo, $userId) {
            $existing = Quotation::where('quotation_number', $quoteNo)->lockForUpdate()->first();
            $nextVersion = $existing ? ($existing->version + 1) : 1;

            if ($existing) {
                // Save previous as revision
                QuotationRevision::create([
                    'quotation_id' => $existing->id,
                    'version'      => $existing->version,
                    'file_path'    => $existing->file_path ?? '',
                    'data'         => array_merge($existing->data ?? [], ['total_amount' => $existing->total_amount]),
                    'created_by'   => $userId,
                ]);

                $existing->fill([
                    'customer_type'    => $data['customer_type'],
                    'customer_id'      => ($data['customer_type'] === 'customer') ? $data['customer_id'] : null,
                    'prospect_id'      => ($data['customer_type'] === 'prospect') ? $data['customer_id'] : null,
                    'payment_term_id'  => $data['payment_term_id'] ?? null,
                    'project_timeline' => $data['project_timeline'] ?? null,
                    'total_amount'     => $data['total_amount'] ?? 0,
                    'status'           => $data['status'] ?? 'Draft',
                    'version'          => $nextVersion,
                    'file_path'        => '', // Reset file path as we generate on-the-fly
                    'data'             => [
                        'products' => $data['products'] ?? [],
                        'discount' => $data['discount'] ?? 0,
                        'global_discount_type' => $data['global_discount_type'] ?? 'percentage',
                        'subject'  => $data['subject'] ?? null,
                        'payment_terms' => $data['payment_terms'] ?? null,
                        'show_payment_terms' => filter_var($data['show_payment_terms'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    ],
                    'updated_by'       => $userId,
                ])->save();

                $payload = $existing->fresh()->toArray();
                $payload['file_url'] = $this->quoteFileUrl($existing, 'current');
                $payload['revised']   = true;
                return response()->json(['message' => 'Quotation revised', 'data' => $payload]);
            }

            $quote = Quotation::create([
                'quotation_number'   => $quoteNo,
                'customer_type'      => $data['customer_type'],
                'customer_id'        => ($data['customer_type'] === 'customer') ? $data['customer_id'] : null,
                'prospect_id'        => ($data['customer_type'] === 'prospect') ? $data['customer_id'] : null,
                'payment_term_id'    => $data['payment_term_id'] ?? null,
                'project_timeline'   => $data['project_timeline'] ?? null,
                'total_amount'       => $data['total_amount'] ?? 0,
                'status'             => $data['status'] ?? 'Draft',
                'version'            => $nextVersion,
                'file_path'          => '',
                'data'               => [
                    'products' => $data['products'] ?? [],
                    'discount' => $data['discount'] ?? 0,
                    'global_discount_type' => $data['global_discount_type'] ?? 'percentage',
                    'subject'  => $data['subject'] ?? null,
                    'payment_terms' => $data['payment_terms'] ?? null,
                    'show_payment_terms' => filter_var($data['show_payment_terms'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ],
                'created_by'         => $userId,
                'updated_by'         => $userId,
            ]);

            $payload = $quote->toArray();
            $payload['file_url'] = $this->quoteFileUrl($quote, 'current');
            $payload['revised']   = false;
            return response()->json(['message' => 'Quotation saved', 'data' => $payload]);
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

    public function download($id)
    {
        $quotation = Quotation::findOrFail($id);
        $data = [
            'customer_type' => $quotation->customer_type,
            'customer_id'   => $quotation->customer_id ?? $quotation->prospect_id,
            'total_amount'  => $quotation->total_amount,
            'version'       => $quotation->version,
            'products'      => $quotation->data['products'] ?? [],
            'discount'      => $quotation->data['discount'] ?? 0,
            'subject'       => $quotation->data['subject'] ?? '',
            'payment_terms' => $quotation->data['payment_terms'] ?? null,
            'show_payment_terms' => (bool)($quotation->data['show_payment_terms'] ?? false),
        ];

        $formattedName = $this->getFormattedQuoteNum($quotation);
        $fileName = $formattedName . ($quotation->version > 1 ? '_v' . $quotation->version : '');
        $binary = $this->generatePdfBinary($data, $formattedName);

        if (is_array($binary) && isset($binary['error'])) {
            abort(500, 'PDF generation failed: ' . $binary['error']);
        }

        return response($binary)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '.pdf"');
    }

    /**
     * View a specific revision PDF generated on-the-fly.
     */
    public function previewRevision(int $id)
    {
        $revision = QuotationRevision::with('quotation')->findOrFail($id);
        $quotation = $revision->quotation;

        $data = [
            'customer_type' => $quotation->customer_type,
            'customer_id'   => $quotation->customer_id ?? $quotation->prospect_id,
            'total_amount'  => $revision->data['total_amount'] ?? 0,
            'version'       => $revision->version,
            'products'      => $revision->data['products'] ?? [],
            'discount'      => $revision->data['discount'] ?? 0,
            'subject'       => $revision->data['subject'] ?? '',
            'payment_terms' => $revision->data['payment_terms'] ?? null,
            'show_payment_terms' => (bool)($revision->data['show_payment_terms'] ?? false),
        ];
        
        // If total_amount wasn't stored in data (for very old revisions), fallback to re-calculating subtotal
        if ($data['total_amount'] == 0) {
            $total = 0;
            foreach($data['products'] as $p) {
                $price = (float)($p['price'] ?? 0);
                $tax = round($price * 0.18, 2);
                $total += ($price + $tax);
            }
            $data['total_amount'] = $total - (float)$data['discount'];
        }

        $formattedName = $this->getFormattedQuoteNum($quotation);
        $revisionName = $formattedName . '_v' . $revision->version;
        $binary = $this->generatePdfBinary($data, $formattedName);

        if (is_array($binary) && isset($binary['error'])) {
            abort(500, 'PDF generation failed: ' . $binary['error']);
        }

        return response($binary)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $revisionName . '.pdf"');
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
     * Helper to get common formatted quote number
     */
    private function getFormattedQuoteNum($quote)
    {
        // Consistency: Always return the saved number from the database record
        return $quote->quotation_number;
    }

    /**
     * Build a secure download URL for a quotation's stored PDF.
     */
    private function quoteFileUrl($quote, $type = 'current')
    {
        if (!$quote) {
            return null;
        }

        if ($type === 'current') {
            return route('quotation.download', ['id' => $quote->id]);
        } else {
            // For revisions, we use a custom route we'll add
            return route('quotation.revision.preview', ['id' => $quote->id]);
        }
    }

    /**
     * Generate PDF binary using chosen template
     */
    private function generatePdfBinary($data, $quoteNumber)
    {
        $settings = DB::table('quotation_settings')->first();
        if (!$settings) {
            $settings = (object) [
                'template_name' => 'triserv',
                'primary_color' => '#434AFA',
                'secondary_color' => '#FF8C00'
            ];
        }

        // Create a temporary quotation object for the view
        $quote = new Quotation();
        $quote->quotation_number = $quoteNumber;
        $quote->version = $data['version'] ?? 1;
        $quote->customer_type = $data['customer_type'];
        $quote->customer_id = ($data['customer_type'] === 'customer') ? $data['customer_id'] : null;
        $quote->prospect_id = ($data['customer_type'] === 'prospect') ? $data['customer_id'] : null;
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
            'subject'  => $data['subject'] ?? '',
            'payment_terms' => $data['payment_terms'] ?? null,
            'show_payment_terms' => $data['show_payment_terms'] ?? false,
        ];

        // Eager load customer if exists
        if ($quote->customer_type == 'customer' && $quote->customer_id) {
            $quote->setRelation('customer', \App\Models\Customer::find($quote->customer_id));
        }

        // Add prospect reference if needed for PDF templates
        if ($quote->customer_type == 'prospect' && $quote->prospect_id) {
            $quote->setRelation('prospect', \App\Models\Prospectus::find($quote->prospect_id));
        }

        $template = $settings->template_name ?? 'triserv';
        $viewPath = "quotation.templates.{$template}";

        if (!view()->exists($viewPath)) {
            $viewPath = 'quotation.templates.triserv';
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
