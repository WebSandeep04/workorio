<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionStatus;

class SubscriptionStatusController extends Controller
{
    public function fetch(Request $request)
    {
        $query = SubscriptionStatus::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('status_name', 'like', "%{$search}%");
        }

        $statuses = $query->paginate(10);
        return response()->json($statuses);
    }

    public function index()
    {
        return view('subscription-status');
    }

    public function store(Request $request)
    {
        $request->validate([
            'status_name' => 'required|string|max:255',
        ]);

        SubscriptionStatus::create([
            'status_name' => $request->status_name,
        ]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $status = SubscriptionStatus::findOrFail($id);
        $status->status_name = $request->status_name;
        $status->save();

        return response()->json(['message' => 'Status updated']);
    }

    public function destroy($id)
    {
        $status = SubscriptionStatus::findOrFail($id);
        $status->delete();

        return response()->json(['message' => 'Status deleted']);
    }

    public function getStatuses()
    {
        try {
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('subscription_status')) {
                return response()->json([]);
            }

            $statuses = SubscriptionStatus::all();
            return response()->json($statuses);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
