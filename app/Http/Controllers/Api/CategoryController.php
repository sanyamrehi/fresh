<?php

namespace App\Http\Controllers\Api;
use App\Models\category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', ''); // Search keyword
        $perPage = $request->input('per_page', 10); // Default 10 per page

        $categories = Category::where('status', 'Active') // Only Active categories
            ->when($search, function ($query, $search) {
                return $query->where('category', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage); // Dynamic pagination

        return response()->json([
            'status' => true,
            'data' => $categories,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validate input
    $request->validate([
        'category' => 'required|string|unique:categories,category',
        'status' => 'nullable|in:Active,InActive',
    ]);

    // Determine the status (default to 'InActive' if not provided)
    $status = $request->status === 'Active' ? 'Active' : 'InActive';

    // Create new category
    $category = Category::create([
        'category' => $request->category,
        'status' => $status,
    ]);

    // Return JSON response
    return response()->json([
        'status' => true,
        'message' => 'Category created successfully',
        'data' => $category,
    ], 201);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $category], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|string|unique:categories,category,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        $status = $request->status === 'Active' ? 'Active' : 'Inactive';

        $category->update([
            'category' => $request->category,
            'status' => $status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully.',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => false, 'message' => 'Category not found'], 404);
        }

        $category->update(['status' => 'Deleted']);

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully.',
        ], 200);
    }
}
