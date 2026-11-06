<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

/**
 * JSON endpoint used by the product form to load the subcategories of the
 * selected category without a full page reload.
 */
class CategorySubcategoryController extends Controller
{
    public function __invoke(Category $category): JsonResponse
    {
        return response()->json(
            $category->subcategories()->get(['id', 'name'])
        );
    }
}
