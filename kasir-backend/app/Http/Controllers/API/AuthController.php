<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi sederhana
        $email = $request->email;
        $password = $request->password;
        
        // Cek manual
        if ($email === 'admin@kasir.com' && $password === 'password') {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => 1,
                        'name' => 'Super Admin',
                        'email' => 'admin@kasir.com',
                        'level' => 'super_admin'
                    ],
                    'token' => '123456'
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'error' => 'Email atau password salah'
        ], 401);
    }
    
    public function logout()
    {
        return response()->json(['success' => true]);
    }
    
    public function me()
    {
        return response()->json(['success' => true]);
    }
}