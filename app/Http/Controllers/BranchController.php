<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    public function index()
    {
        return view('master.branches');
    }

    public function list(Request $request): JsonResponse
    {
        $query = Branch::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%')
                  ->orWhere('location', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->has('page')) {
            return response()->json($query->orderBy('name')->paginate(10));
        }
        
        return response()->json($query->orderBy('name')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateBranch($request);
        if (empty($data['code'])) {
            $data['code'] = $this->generateTempCode('branch');
        }

        $branch = Branch::create($data);
        $branch->update(['code' => $this->formatBranchCode($branch->id)]);

        return response()->json([
            'success' => true,
            'message' => 'Branch saved successfully.',
            'branch' => $branch,
        ]);
    }

    public function update(Request $request, $branchId): JsonResponse
    {
        $branch = Branch::findOrFail($branchId);
        $request->merge(['code' => $branch->code]);

        $data = $this->validateBranch($request, $branch->id);
        $branch->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Branch updated successfully.',
            'branch' => $branch,
        ]);
    }

    public function destroy($branchId): JsonResponse
    {
        $branch = Branch::findOrFail($branchId);

        if ($branch->departments()->exists() || $branch->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete branch with departments or employees.',
            ], 422);
        }

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully.',
        ]);
    }

    private function validateBranch(Request $request, ?int $branchId = null): array
    {
        return $request->validate([
            'code' => 'nullable|string|max:50|unique:branches,code,' . $branchId,
            'name' => 'required|string|max:255|unique:branches,name,' . $branchId,
            'location' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:150',
            'contact_phone' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);
    }

    private function generateTempCode(string $prefix): string
    {
        return $prefix . '-temp-' . Str::uuid()->toString();
    }

    private function formatBranchCode(int $id): string
    {
        return 'Branch-' . $id;
    }
}

