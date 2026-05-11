<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'tanggal_pembalain', 'fold_langs', 'tid_admin', 
        'tid_prodax', 'detail', 'tid_kasir', 'load_keunkungan',
        'bayar', 'kembalian'
    ];

    protected $casts = [
        'detail' => 'array',
        'tanggal_pembalain' => 'date',
        'load_keunkungan' => 'decimal:2',
        'bayar' => 'decimal:2',
        'kembalian' => 'decimal:2'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'tid_admin');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'tid_prodax');
    }

    public function kasir()
    {
        return $this->belongsTo(Admin::class, 'tid_kasir');
    }
}