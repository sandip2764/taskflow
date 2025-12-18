<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * List all categories
     */
    public function index()
    {
        $categories = Category::withCount('tasks')->get();

        return response()->json([
            'categories' => $categories,
        ], 200);
    }

    /**
     * Store new category
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message'  => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Show category with tasks
     */
    public function show($id)
    {
        $category = Category::with('tasks')->findOrFail($id);

        return response()->json([
            'category' => $category,
        ],200);
    }
}
