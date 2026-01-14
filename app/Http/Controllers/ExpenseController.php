<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expenses');
    }

    public function fetchExpenses(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $expenses = $query->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($expenses);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        Expense::create([
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['success' => true]);
    }
}
