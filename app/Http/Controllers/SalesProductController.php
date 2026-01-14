<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesProduct;

class SalesProductController extends Controller
{
    public function fetchSalesProducts(Request $request)
    {
        try {
             // Check if sales_products table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_products')) {
                return response()->json([]);
            }

            $query = SalesProduct::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('product_name', 'like', "%{$search}%");
            }

            $products = $query->paginate(10);
            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function index()
    {
        $products = SalesProduct::paginate(10);
        return view('product');
    }

    public function update(Request $request, $id)
    {
        $product = SalesProduct::findOrFail($id);
        $product->product_name = $request->product_name;
        $product->save();

        return response()->json(['message' => 'product updated']);
    }

    public function destroy($id)
    {
        $product = SalesProduct::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'product deleted']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255']);

        SalesProduct::create([
            'product_name' => $request->product_name]);

        return response()->json(['success' => true]);
    }

    public function getproduct(){
        try {
            // Check if sales_products table exists
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('sales_products')) {
                return response()->json([]);
            }

            $products = SalesProduct::get(); 
            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
