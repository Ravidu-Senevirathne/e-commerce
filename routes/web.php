<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController as AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Test route for admin role checking
Route::get('/check-roles', function () {
    if (!auth()->check()) {
        return "Not logged in. Please <a href='/login'>login</a> first.";
    }

    $user = auth()->user();
    $roles = $user->roles()->pluck('name')->toArray();
    $isAdmin = $user->hasRole('admin');

    return response()->json([
        'user' => $user->email,
        'roles' => $roles,
        'isAdmin' => $isAdmin,
        'permissions' => $user->getAllPermissions()->pluck('name'),
        'user_model_class' => get_class($user),
        'session_id' => session()->getId()
    ]);
});

Route::resource('products', ProductController::class);

Route::get('/dashboard', function () {
    // Redirect admin users to admin dashboard
    if (auth()->check() && auth()->user()->hasRole('admin')) {
        \Log::info('Dashboard route: Redirecting admin user to admin dashboard: ' . auth()->user()->email);
        return redirect()->route('admin.dashboard');
    }
    \Log::info('Dashboard route: Regular user accessing dashboard: ' . (auth()->check() ? auth()->user()->email : 'unauthenticated'));
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('dashboard');

    // Product Routes
    Route::resource('products', AdminProductController::class);

    // User Routes
    Route::resource('users', UserController::class);
});

require __DIR__ . '/auth.php';
