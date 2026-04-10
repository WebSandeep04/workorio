<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CallingList;
use App\Models\Calling;
use App\Models\TenantDatabaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CallingListController extends Controller
{
    public function index()
    {
        return view('calling.list.index');
    }

    public function getData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $lists = CallingList::orderBy('id', 'desc')->paginate($perPage);
        $totalLeads = CallingList::sum('total_records');
        
        return response()->json([
            'lists' => $lists,
            'total_leads' => $totalLeads
        ]);
    }

    public function create()
    {
        return view('calling.list.create');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Name', 'Email', 'Phone', 'Address', 'City', 'State', 
            'Company Name', 'Contact Person', 'Legal Status', 'GST Number', 'Turnover'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            // Dummy Row 1
            fputcsv($file, [
                'John Doe', 'john@example.com', '9876543210', '123 Business Park', 'Mumbai', 'Maharashtra',
                'Global Tech Solutions', 'Ms. Jane Smith', 'Private Ltd', '27AAACG6742D1Z5', '50 Crores'
            ]);
            
            // Dummy Row 2
            fputcsv($file, [
                'Robert Wilson', 'robert@corp.com', '9876543211', 'Sector 5, Salt Lake', 'Kolkata', 'West Bengal',
                'Eastern Enterprises', 'Mr. Amit Das', 'Partnership', '19BBBCG1234E1Z0', '10 Crores'
            ]);

            fclose($file);
        };

        $headers_response = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=calling_leads_template.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return response()->stream($callback, 200, $headers_response);
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $list = CallingList::findOrFail($id);
            // Delete associated leads
            Calling::where('list_id', $id)->delete();
            $list->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'List and associated leads removed successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'excel_file' => 'required|file|mimes:csv,txt,xlsx,xls'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $list = CallingList::create([
            'name' => $request->name,
            'total_records' => 0
        ]);

        $file = $request->file('excel_file');
        $filePath = $file->getRealPath();
        
        $records = [];
        $header = null;
        $total = 0;

        // Simple CSV parser for now since Maatwebsite is missing
        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), ['csv', 'txt'])) {
            return back()->with('error', 'Only CSV and TXT files are supported for now. Please convert your Excel file to CSV.')->withInput();
        }

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (empty(array_filter($data))) continue; // Skip empty rows

                if (!$header) {
                    $header = array_map('trim', $data);
                    continue;
                }
                
                // Ensure same number of elements to avoid array_combine error
                if (count($header) !== count($data)) {
                    // Try to pad or truncate data to match header
                    if (count($data) < count($header)) {
                        $data = array_pad($data, count($header), null);
                    } else {
                        $data = array_slice($data, 0, count($header));
                    }
                }

                $row = array_combine($header, $data);
                
                // Map columns (adjust based on expected excel format)
                $records[] = [
                    'list_id' => $list->id,
                    'name'    => $row['Name'] ?? ($row['name'] ?? null),
                    'company_name'   => $row['Company Name'] ?? ($row['company_name'] ?? ($row['Company'] ?? ($row['company'] ?? null))),
                    'contact_person' => $row['Contact Person'] ?? ($row['contact_person'] ?? ($row['Contact person'] ?? null)),
                    'email'   => $row['Email'] ?? ($row['email'] ?? null),
                    'phone'   => $row['Phone'] ?? ($row['phone'] ?? ($row['Contact'] ?? ($row['Mobile'] ?? null))),
                    'address' => $row['Address'] ?? ($row['address'] ?? null),
                    'city'    => $row['City'] ?? ($row['city'] ?? null),
                    'state'   => $row['State'] ?? ($row['state'] ?? null),
                    'legal_status' => $row['Legal Status'] ?? ($row['legal_status'] ?? null),
                    'gst_number'   => $row['GST Number'] ?? ($row['gst_number'] ?? ($row['GST'] ?? null)),
                    'turnover'     => $row['Turnover'] ?? ($row['turnover'] ?? ($row['Turn Over'] ?? null)),
                ];
                $total++;
                
                // Chunk insert to avoid memory issues
                if (count($records) >= 500) {
                    Calling::insert($records);
                    $records = [];
                }
            }
            fclose($handle);
        }

        if (!empty($records)) {
            Calling::insert($records);
        }

        $list->update(['total_records' => $total]);

        return redirect()->route('calling.list.index')->with('success', "List '$request->name' uploaded successfully with $total records.");
    }
}
