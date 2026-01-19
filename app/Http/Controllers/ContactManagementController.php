<?php

namespace App\Http\Controllers;

use App\Models\BusinessCardScan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContactManagementController extends Controller
{
    public function index()
    {
        return view('contact-management.index');
    }

    public function getSummaryStats(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $userId = session('user_id');
        $roleId = session('user_role');
        
        $query = BusinessCardScan::query();
        
        // If user is not admin, or if they are admin but haven't requested to view all, limit to their own
        if (!($roleId == 1 && $request->view_all === 'true')) {
             $query->where('created_by', $userId);
        }
        
        $totalContacts = (clone $query)->count();
        $newToday = (clone $query)->whereDate('created_at', $today)->count();
        $withEmail = (clone $query)->whereNotNull('email')->where('email', '!=', '')->count();
        $withPhone = (clone $query)->whereNotNull('phone_primary')->where('phone_primary', '!=', '')->count();

        return response()->json([
            'total_contacts' => $totalContacts,
            'new_today' => $newToday,
            'with_email' => $withEmail,
            'with_phone' => $withPhone
        ]);
    }

    public function fetch(Request $request)
    {
        $userId = session('user_id');
        $roleId = session('user_role');
        $query = BusinessCardScan::query();
        
        // Admin View All Logic
        if (!($roleId == 1 && $request->view_all === 'true')) {
             $query->where('created_by', $userId);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_primary', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($contacts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_primary' => 'nullable|string|max:20',
        ]);

        $data = $request->all();
        $data['created_by'] = session('user_id'); // Track who created it

        $contact = BusinessCardScan::create($data);

        return response()->json(['success' => true, 'message' => 'Contact added successfully', 'data' => $contact]);
    }

    public function edit($id)
    {
        $query = BusinessCardScan::where('id', $id);
        $roleId = session('user_role');
        $userId = session('user_id');
        
        if ($roleId != 1) {
            $query->where('created_by', $userId);
        }
        
        $contact = $query->first();

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contact not found or you do not have permission'], 404);
        }
        return response()->json($contact);
    }

    public function update(Request $request, $id)
    {
        $query = BusinessCardScan::where('id', $id);
        $roleId = session('user_role');
        $userId = session('user_id');
        
        if ($roleId != 1) {
            $query->where('created_by', $userId);
        }
        
        $contact = $query->first();

        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contact not found or you do not have permission'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_primary' => 'nullable|string|max:20',
        ]);

        $contact->update($request->all());

        return response()->json(['success' => true, 'message' => 'Contact updated successfully']);
    }

    public function destroy($id)
    {
        $query = BusinessCardScan::where('id', $id);
        $roleId = session('user_role');
        $userId = session('user_id');
        
        if ($roleId != 1) {
            $query->where('created_by', $userId);
        }
        
        $contact = $query->first();
        
        if (!$contact) {
            return response()->json(['success' => false, 'message' => 'Contact not found or you do not have permission'], 404);
        }

        $contact->delete(); // Uses SoftDeletes from migration

        return response()->json(['success' => true, 'message' => 'Contact deleted successfully']);
    }
}
