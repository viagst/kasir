<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'product';
    protected $fillable = [
        'name_prodax', 'stock', 'model', 'hangs_tail', 'keunkungan',
        'tid_kategori', 'gamber', 'dekelpjei', 'ketterkedaan', 'esimasi'
    ];

    protected $casts = [
        'hangs_tail' => 'decimal:2',
        'keunkungan' => 'decimal:2'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'tid_kategori');
    }
}