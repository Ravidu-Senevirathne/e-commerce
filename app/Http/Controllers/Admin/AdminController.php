<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function dashboard()
    {
        // Verify admin access
        if (!auth()->user()->hasRole('admin')) {
            Log::warning('Non-admin user attempted to access admin dashboard: ' . auth()->user()->email);
            return redirect()->route('dashboard');
        }

        Log::info('Admin user accessed dashboard: ' . auth()->user()->email);

        // Get statistics for dashboard
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $featuredProducts = Product::where('featured', true)->count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'featuredProducts'
        ));
    }
}
