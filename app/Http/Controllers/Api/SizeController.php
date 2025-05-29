<?php

namespace App\Http\Controllers\Api;
use App\Models\size;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', ''); // Get search input, default to an empty string
        $perPage = $request->input('per_page', 10); // Number of items per page (default 10)

        $sizes = Size::where('status', 'Active') // Only fetch Active sizes
            ->when($search, function ($query, $search) {
                return $query->where('size', 'like', '%' . $search . '%');
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage); // Paginate results dynamically

        return response()->json([
            'status' => true,
            'data' => $sizes,
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'size' => 'required|string|unique:sizes,size',
            'status' => 'nullable|in:Active,InActive',
        ]);

        // Determine the status (default to 'InActive' if not provided)
        $status = $request->status === 'Active' ? 'Active' : 'InActive';

        // Create new size
        $size = Size::create([
            'size' => $request->size,
            'status' => $status,
        ]);

        // Return JSON response
        return response()->json([
            'status' => true,
            'message' => 'Size created successfully',
            'data' => $size,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['status' => false, 'message' => 'Size not found'], 404);
        }

        return response()->json(['status' => true, 'data' => $size], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['status' => false, 'message' => 'Size not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'size' => 'required|string|unique:sizes,size,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 400);
        }

        $status = $request->status === 'Active' ? 'Active' : 'Inactive';

        $size->update([
            'size' => $request->size,
            'status' => $status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Size updated successfully.',
            'data' => $size,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $size = Size::find($id);

        if (!$size) {
            return response()->json(['status' => false, 'message' => 'Size not found'], 404);
        }

        $size->update(['status' => 'Deleted']);

        return response()->json([
            'status' => true,
            'message' => 'Size deleted successfully.',
        ], 200);
    }
}
