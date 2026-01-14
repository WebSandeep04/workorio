<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CountryController extends Controller
{
    public function index()
    {
        return view('countries');
    }

    public function list(Request $request): JsonResponse
    {
        $query = Country::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $countries = $query->orderBy('name')->paginate(10);

        return response()->json($countries);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateCountry($request);
        if (empty($data['code'])) {
            $data['code'] = $this->generateTempCode();
        }

        $country = Country::create($data);
        $country->update(['code' => $this->formatCode($country->id)]);

        return response()->json([
            'success' => true,
            'message' => 'Country saved successfully.',
            'country' => $country,
        ]);
    }

    public function update(Request $request, $countryId): JsonResponse
    {
        $country = Country::findOrFail($countryId);
        $request->merge(['code' => $country->code]);

        $data = $this->validateCountry($request, $country->id);
        $country->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Country updated successfully.',
            'country' => $country,
        ]);
    }

    public function destroy($countryId): JsonResponse
    {
        $country = Country::findOrFail($countryId);

        if ($country->employees()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete country linked with employees.',
            ], 422);
        }

        $country->delete();

        return response()->json([
            'success' => true,
            'message' => 'Country deleted successfully.',
        ]);
    }

    private function validateCountry(Request $request, ?int $countryId = null): array
    {
        return $request->validate([
            'code' => 'nullable|string|max:10|unique:countries,code,' . $countryId,
            'name' => 'required|string|max:255|unique:countries,name,' . $countryId,
            'status' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);
    }

    private function generateTempCode(): string
    {
        return 'country-temp-' . Str::uuid()->toString();
    }

    private function formatCode(int $id): string
    {
        return 'Country-' . $id;
    }
}

