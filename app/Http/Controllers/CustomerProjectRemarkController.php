<?php

namespace App\Http\Controllers;

use App\Models\CustomerProject;
use App\Models\CustomerProjectRemark;
use Illuminate\Http\Request;

class CustomerProjectRemarkController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_project_id' => 'required|exists:customer_projects,id',
            'remark' => 'required|string',
        ]);

        $userId = session('user_id');
        $user = \App\Models\User::find($userId);

        $remark = CustomerProjectRemark::create([
            'customer_project_id' => $validated['customer_project_id'],
            'user_id' => $userId,
            'remark' => $validated['remark'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Remark saved successfully',
            'data' => [
                'remark' => $remark->remark,
                'user_name' => $user ? $user->name : 'Unknown',
                'created_at' => $remark->created_at->format('d M Y, h:i A')
            ]
        ]);
    }

    public function latest($projectId)
    {
        $remark = CustomerProjectRemark::where('customer_project_id', $projectId)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'remark' => $remark ? $remark->remark : null
        ]);
    }
}
