<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function index()
    {
        try {
            $kategori = Kategori::all();
            return response()->json([
                'success' => true, 
                'data' => $kategori
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data kategori'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:255',
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
                $data['gamber'] = $request->file('gamber')->store('kategori', 'public');
            }

            $kategori = Kategori::create($data);
            
            return response()->json([
                'success' => true, 
                'data' => $kategori,
                'message' => 'Kategori berhasil dibuat'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat kategori'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kategori = Kategori::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'error' => 'Kategori tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true, 
                'data' => $kategori
            ]);
                
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data kategori'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kategori' => 'required|string|max:255',
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
            $kategori = Kategori::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'error' => 'Kategori tidak ditemukan'
                ], 404);
            }

            $data = $request->all();
            
            if ($request->hasFile('gamber')) {
                // Delete old image
                if ($kategori->gamber && Storage::disk('public')->exists($kategori->gamber)) {
                    Storage::disk('public')->delete($kategori->gamber);
                }
                $data['gamber'] = $request->file('gamber')->store('kategori', 'public');
            }

            $kategori->update($data);
            
            return response()->json([
                'success' => true, 
                'data' => $kategori,
                'message' => 'Kategori berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal update kategori'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $kategori = Kategori::find($id);
            
            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'error' => 'Kategori tidak ditemukan'
                ], 404);
            }

            // Delete image if exists
            if ($kategori->gamber && Storage::disk('public')->exists($kategori->gamber)) {
                Storage::disk('public')->delete($kategori->gamber);
            }

            $kategori->delete();
            
            return response()->json([
                'success' => true, 
                'message' => 'Kategori berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus kategori'
            ], 500);
        }
    }
}