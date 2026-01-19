<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;
use Illuminate\Support\Facades\Schema;

class SupplierController extends Controller
{
    public function fetch(Request $request)
    {
        // Gracefully handle missing table if migration hasn't stuck yet
        if (!Schema::hasTable('suppliers')) {
             return response()->json([
                'current_page' => 1,
                'data' => [],
                'total' => 0,
                'last_page' => 1
             ]);
        }

        $query = Supplier::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%')
                  ->orWhere('mobile', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('city', 'like', '%' . $search . '%');
            });
        }

        $suppliers = $query->latest()->paginate(10);
        return response()->json($suppliers);
    }

    public function index()
    {
        if(request()->ajax()) {
            return $this->fetch(request());
        }
        
        return view('software-setup.supplier.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->all());

        return response()->json(['message' => 'Supplier updated']);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:20',
        ]);

        Supplier::create($request->all());

        return response()->json(['success' => true]);
    }
}
