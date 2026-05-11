<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = Product::with('kategori')->get();
            return response()->json([
                'success' => true, 
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data product'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_prodax' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'hangs_tail' => 'required|numeric|min:0',
            'tid_kategori' => 'required|exists:kategori,id',
            'gamber' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            
            if ($request->hasFile('gamber')) {
                $data['gamber'] = $request->file('gamber')->store('products', 'public');
            }

            $product = Product::create($data);
            
            return response()->json([
                'success' => true, 
                'data' => $product->load('kategori'),
                'message' => 'Product berhasil dibuat'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat product'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::with('kategori')->find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true, 
                'data' => $product
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data product'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name_prodax' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'hangs_tail' => 'required|numeric|min:0',
            'tid_kategori' => 'required|exists:kategori,id',
            'gamber' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product tidak ditemukan'
                ], 404);
            }

            $data = $request->all();
            
            if ($request->hasFile('gamber')) {
                // Delete old image
                if ($product->gamber && Storage::disk('public')->exists($product->gamber)) {
                    Storage::disk('public')->delete($product->gamber);
                }
                $data['gamber'] = $request->file('gamber')->store('products', 'public');
            }

            $product->update($data);
            
            return response()->json([
                'success' => true, 
                'data' => $product->load('kategori'),
                'message' => 'Product berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal update product'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'error' => 'Product tidak ditemukan'
                ], 404);
            }

            // Delete image if exists
            if ($product->gamber && Storage::disk('public')->exists($product->gamber)) {
                Storage::disk('public')->delete($product->gamber);
            }

            $product->delete();
            
            return response()->json([
                'success' => true, 
                'message' => 'Product berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus product'
            ], 500);
        }
    }

    public function getByKategori($id)
    {
        try {
            $products = Product::where('tid_kategori', $id)
                ->with('kategori')
                ->get();
                
            return response()->json([
                'success' => true, 
                'data' => $products
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data product'
            ], 500);
        }
    }
}