<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the landing page.
     */
    public function index()
    {
        $featuredProducts = Product::where('featured', true)->take(4)->get();
        $latestProducts = Product::latest()->take(8)->get();

        return view('home', compact('featuredProducts', 'latestProducts'));
    }
}
