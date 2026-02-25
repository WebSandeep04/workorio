# Proposal: Template-Based PDF Generation (Laravel Blade + Snappy/DomPDF)

## Overview
Currently, the system generates PDFs on the client side using JavaScript (jsPDF). In a SaaS environment where different clients (tenants) require unique quotation layouts, moving to **Server-Side Blade Templates** is the most scalable approach. This allows you to create separate HTML/CSS files for each "Pattern" and render them into professional PDFs.

---

## 1. Architectural Changes

### Database Updates
We need to track which template each client wants to use.
- **Table:** `quotation_settings`
- **New Column:** `template_name` (e.g., `modern`, `classic`, `compact`)
- **New Column:** `custom_css` (Optional: Allows clients to add their own brand colors/fonts).

### File Structure
We will organize templates in a dedicated folder:
```text
resources/views/quotation/templates/
├── base_layout.blade.php  (Common CSS, Header/Footer headers)
├── modern.blade.php       (Pattern A)
├── classic.blade.php      (Pattern B)
└── professional.blade.php (Pattern C)
```

---

## 2. The Implementation Logic

### Step A: The Controller Flow
Instead of sending a Base64 string from the frontend, the frontend will simply save the data, and the backend will generate the PDF.

```php
// In QuotationController.php
public function generatePdf($id) {
    $quotation = Quotation::with(['customer', 'products'])->findOrFail($id);
    $settings = DB::table('quotation_settings')->first();
    
    // Determine which template to use
    $template = $settings->template_name ?? 'default';
    
    // Pass data to the view and convert to PDF
    $pdf = PDF::loadView("quotation.templates.{$template}", [
        'quote' => $quotation,
        'settings' => $settings
    ]);

    return $pdf->stream("Quotation_{$quotation->quotation_number}.pdf");
}
```

### Step B: The Blade Template (HTML/CSS)
Using Blade allows you to use standard CSS (Flexbox, Grid) which is much easier than JS coordinates.

```html
<!-- resources/views/quotation/templates/modern.blade.php -->
<style>
    .header { color: {{ $settings->primary_color }}; }
    .table th { background: #f3f4f6; }
</style>

<div class="header">
    <h1>QUOTATION</h1>
    <p>{{ $settings->company_name }}</p>
</div>

<table>
    @foreach($quote->products as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->price }}</td>
        </tr>
    @endforeach
</table>
```

---

## 3. Recommended PDF Engine

To convert Blade to PDF, I recommend one of these two Laravel-friendly libraries:

1.  **[Laravel-DomPDF](https://github.com/barryvdh/laravel-dompdf):** 
    - *Best for:* Simple, clean layouts. 
    - *Pros:* Easy to install, no external dependencies.
2.  **[Browsershot (Spatie)](https://github.com/spatie/browsershot):**
    - *Best for:* Highly advanced SaaS designs (vibrant colors, modern CSS).
    - *Pros:* Uses Google Chrome to "print" the PDF, supporting full CSS/JS.

---

## 4. Why this is better for SaaS

1.  **Layout Variety:** You can offer a "Template Gallery" to your clients. Client A selects "Pattern 1", Client B selects "Pattern 2".
2.  **Customization:** You can let high-tier clients upload their own `header.png` or `footer.png`.
3.  **Consistency:** The PDF will look exactly the same regardless of the user's browser or device (unlike jsPDF).
4.  **Bulk Processing:** You can easily generate and email 100 PDFs in the background using Laravel Jobs.

---

## Next Steps
When you are ready to implement:
1. We will install the chosen PDF library via Composer.
2. We will create the first `base_layout` and a `modern` template.
3. We will modify the `store` method to trigger the server-side generation.
