<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class AdminController extends Controller
{
    public function show($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->level !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 403);
            }
            
            // Your logic here for show method
            return response()->json([
                'success' => true,
                'data' => [] // your data here
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil admin: '. $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->level !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 403);
            }
            
            // Your logic here for index method
            return response()->json([
                'success' => true,
                'data' => [] // your data here
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengambil data admin: '. $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->level !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 403);
            }
            
            // Your logic here for store method
            return response()->json([
                'success' => true,
                'message' => 'Admin created successfully'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal membuat admin: '. $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->level !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 403);
            }
            
            // Your logic here for update method
            return response()->json([
                'success' => true,
                'message' => 'Admin updated successfully'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal update admin: '. $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            
            if (!$user || $user->level !== 'super_admin') {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized access'
                ], 403);
            }
            
            // Your logic here for destroy method
            return response()->json([
                'success' => true,
                'message' => 'Admin deleted successfully'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menghapus admin: '. $e->getMessage()
            ], 500);
        }
    }
}