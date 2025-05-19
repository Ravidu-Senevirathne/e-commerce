<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $totalProducts = Product::count();
        $totalUsers = User::count();
        $featuredProducts = Product::where('featured', true)->count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'featuredProducts'
        ));
    }

    /**
     * Display a list of users.
     */
    public function users()
    {
        $users = User::with('role')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Remove the specified user.
     */
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete yourself!');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully!');
    }
}
