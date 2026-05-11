<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Product;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // TAMBAH INI

class TransaksiController extends Controller
{
    // ... method lainnya tetap sama ...

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:product,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'fold_langs' => 'required|in:tunai,non_tunai'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // Get current user
                $user = Auth::guard('sanctum')->user();
                
                $totalKeuntungan = 0;
                $details = [];
                $productsToUpdate = [];

                // Validasi stock dan hitung total
                foreach ($request->items as $item) {
                    $product = Product::find($item['product_id']);
                    
                    if (!$product) {
                        throw new \Exception("Produk dengan ID {$item['product_id']} tidak ditemukan");
                    }
                    
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock {$product->name_prodax} tidak cukup. Tersedia: {$product->stock}");
                    }

                    $harga_jual = (float) $product->hangs_tail;
                    $subtotal = $item['quantity'] * $harga_jual;
                    $totalKeuntungan += $subtotal;

                    $details[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name_prodax,
                        'quantity' => $item['quantity'],
                        'price' => $harga_jual,
                        'subtotal' => $subtotal
                    ];

                    $productsToUpdate[] = [
                        'product' => $product,
                        'quantity' => $item['quantity']
                    ];
                }

                // Validasi total amount
                if ($totalKeuntungan != $request->total_amount) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Total amount tidak sesuai dengan perhitungan sistem'
                    ], 422);
                }

                // Calculate kembalian
                $kembalian = $request->bayar - $request->total_amount;
                
                if ($kembalian < 0) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Pembayaran kurang'
                    ], 422);
                }

                // Create transaksi
                $transaksi = Transaksi::create([
                    'tanggal_pembalain' => now()->format('Y-m-d'),
                    'fold_langs' => $request->fold_langs,
                    'tid_admin' => $user->id, // PAKAI $user->id
                    'tid_prodax' => $request->items[0]['product_id'],
                    'detail' => json_encode($details),
                    'tid_kasir' => $user->id, // PAKAI $user->id
                    'load_keunkungan' => $totalKeuntungan,
                    'bayar' => $request->bayar,
                    'kembalian' => $kembalian
                ]);

                // Update stock untuk semua produk
                foreach ($productsToUpdate as $item) {
                    $item['product']->decrement('stock', $item['quantity']);
                }

                $transaksi->load(['admin', 'product', 'kasir']);
                
                return response()->json([
                    'success' => true,
                    'data' => $transaksi,
                    'message' => 'Transaksi berhasil dibuat'
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    // ... method lainnya ...
}