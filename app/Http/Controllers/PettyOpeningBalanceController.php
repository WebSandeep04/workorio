<?php

namespace App\Http\Controllers;

use App\Models\PettyOpeningBalance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PettyOpeningBalanceController extends Controller
{
    public function index()
    {
        $departments = \App\Models\Department::all();
        return view('opening-balance', compact('departments'));
    }

    public function fetch(Request $request)
    {
        $query = PettyOpeningBalance::with('department')->orderBy('created_at', 'desc');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            // Search by amount
            $query->where('amount', 'like', "%{$search}%");
        }

        $data = $query->paginate(10);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id'
        ]);

        PettyOpeningBalance::create([
            'amount' => $request->amount,
            'department_id' => $request->department_id,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $balance = PettyOpeningBalance::findOrFail($id);
        
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'department_id' => 'required|exists:departments,id'
        ]);

        $balance->update([
            'amount' => $request->amount,
            'department_id' => $request->department_id
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $balance = PettyOpeningBalance::findOrFail($id);
        $balance->delete();
        return response()->json(['success' => true]);
    }
}
