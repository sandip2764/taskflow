<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * List all categories
     */
    public function index()
    {
        $categories = Category::withCount('tasks')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store new category
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);

    }

    /**
     * Show category with tasks
     */
    public function show($id)
    {
        $category = Category::with('tasks')->findOrFail($id);

        return new CategoryResource($category);
    }
}
