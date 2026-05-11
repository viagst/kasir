<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $fillable = ['kategori', 'gamber'];
    
    public function products()
    {
        return $this->hasMany(Product::class, 'tid_kategori');
    }
}