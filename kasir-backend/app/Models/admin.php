<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory;
    
    protected $table = 'admin';
    protected $fillable = ['email', 'username', 'password', 'gamber', 'level'];
    protected $hidden = ['password'];

    // Tambahkan guard untuk admin
    protected $guard = 'admin';
}