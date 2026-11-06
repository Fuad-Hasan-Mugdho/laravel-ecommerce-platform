<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'categories' => Category::count(),
                'subcategories' => Subcategory::count(),
                'products' => Product::count(),
                'activeProducts' => Product::active()->count(),
            ],
            'latestProducts' => Product::with(['category', 'subcategory'])->latest()->take(5)->get(),
        ]);
    }
}
