<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\product;
use App\Models\category;
use App\Models\size;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index(Request $request)
     {
         $search = $request->input('search', '');
         $customerId = $request->input('user_id'); // Get Logged-in User ID

         // ✅ Fetch user addresses
         $addresses = Address::where('user_id', $customerId)->get();

         // ✅ Fetch Cart Products for Logged-in User
         $cartProducts = Cart::where('user_id', $customerId)
             ->leftJoin('products', 'cart.product_id', '=', 'products.id')
             ->select('cart.*', 'products.name', 'products.price', 'products.image')
             ->get();

         // ✅ Fetch Products with related data
         $products = Product::select('products.*')
             ->selectRaw('(SELECT GROUP_CONCAT(sizes.size) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) as sizes')
             ->selectRaw('(SELECT GROUP_CONCAT(categories.category) FROM categories WHERE FIND_IN_SET(categories.id, products.category)) as categories')
             ->where('products.status', 'Active')
             ->when($search, function ($query, $search) {
                 $query->where(function ($subQuery) use ($search) {
                     $subQuery->where('products.name', 'like', '%' . $search . '%')
                              ->orWhere('products.price', 'like', '%' . $search . '%')
                              ->orWhereRaw('(SELECT GROUP_CONCAT(categories.category) FROM categories WHERE FIND_IN_SET(categories.id, products.category)) LIKE ?', ["%{$search}%"])
                              ->orWhereRaw('(SELECT GROUP_CONCAT(sizes.size) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) LIKE ?', ["%{$search}%"]);
                 });
             })
             ->orderBy('products.id', 'desc')
             ->paginate(6);

         // ✅ Return API Response
         return response()->json([
             'status' => true,
             'message' => 'Products & Cart List Retrieved Successfully!',
             'products' => $products->items(),
             'cart' => $cartProducts,
             'pagination' => [
                 'current_page' => $products->currentPage(),
                 'per_page' => $products->perPage(),
                 'total' => $products->total(),
                 'links' => $products->links('pagination::bootstrap-5')->render(),
             ]
         ], 200);
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'size' => 'nullable|array', // Expecting an array of size IDs
            'color' => 'nullable|string',
            'category' => 'nullable|array', // Expecting an array of category IDs
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:Active,InActive',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('fresh_images'), $imageName);
            $imagePath = '' . $imageName; // Fixed image path
        }

        // Store product with IDs
        $sizeIds = isset($request->size) ? implode(',', $request->size) : null;
        $categoryIds = isset($request->category) ? implode(',', $request->category) : null;

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'size' => $sizeIds, // Store as CSV
            'color' => $request->color,
            'category' => $categoryIds, // Store as CSV
            'image' => $imagePath,
            'status' => $request->status ?? 'InActive',
        ]);

        // Fetch multiple values correctly
        $sizes = $sizeIds ? Size::whereIn('id', explode(',', $sizeIds))->get(['id', 'size']) : [];
        $categories = $categoryIds ? Category::whereIn('id', explode(',', $categoryIds))->get(['id', 'category']) : [];

        return response()->json([
            'status' => true,
            'message' => 'Product created successfully',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 2),   
                'size_ids' => $sizeIds ? explode(',', $sizeIds) : [],
                'sizes' => $sizes, // Fetch full size data
                'category_ids' => $categoryIds ? explode(',', $categoryIds) : [],
                'categories' => $categories, // Fetch full category data
                'color' => $product->color,
                'image' => $imagePath, // Ensure image URL is valid
                'status' => $product->status,
                'created_at' => $product->created_at->toDateTimeString(),
                'updated_at' => $product->updated_at->toDateTimeString(),
            ],
        ], 201);
        // dd($imagePath);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $customerId = auth()->id(); // Get logged-in user ID

        // ✅ Retrieve the selected address ID from URL parameters
        $selectedAddressId = $request->query('address_id');

        // ✅ Get user addresses
        $addresses = Address::where('user_id', $customerId)->get();

        // ✅ Get product details with size and category
        $product = Product::select('products.*')
            ->selectRaw('(SELECT GROUP_CONCAT(sizes.size) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) as sizes')
            ->selectRaw('(SELECT GROUP_CONCAT(categories.category) FROM categories WHERE FIND_IN_SET(categories.id, products.category)) as categories')
            ->where('products.id', $id)
            ->where('products.status', 'Active')
            ->first();

        // If product not found
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Format response data
        $response = [
            'status' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 2),
                'color' => $product->color,
                'sizes' => explode(',', $product->sizes),
                'categories' => explode(',', $product->categories),
                'status' => $product->status,
                'image' => $product->image ? url($product->image) : null,
                'created_at' => $product->created_at->toDateTimeString(),
                'updated_at' => $product->updated_at->toDateTimeString(),
            ],
            'addresses' => $addresses->map(function ($address) {
                return [
                    'id' => $address->id,
                    'address' => $address->address,
                    'city' => $address->city,
                    'state' => $address->state,
                    'pincode' => $address->pincode,
                    'user_id' =>  $customerId,
                ];
            }),
            'selected_address_id' => $selectedAddressId,
        ];

        return response()->json($response, 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function edit($id)
    {
        // Fetch the product
        $product = Product::findOrFail($id);

        // Convert comma-separated values into arrays
        $selectedSizes = explode(',', $product->size);
        $selectedCategories = explode(',', $product->category);

        // Fetch all available sizes and categories
        $sizes = Size::all();
        $categories = Category::all();

        return response()->json([
            'status' => true,
            'product' => $product,
            'sizes' => $sizes,
            'categories' => $categories,
            'selected_sizes' => $selectedSizes,
            'selected_categories' => $selectedCategories
        ], 200);
    }

    /**
     * Update a product.
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|unique:products,name,' . $id,
        ]);

        $product = Product::findOrFail($id);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('fresh_images'), $imagePath);
        } else {
            $imagePath = $product->image; // Keep the existing image
        }

        // Process sizes and categories
        $sizes = is_array($request->size_size) ? implode(',', $request->size_size) : ($request->size_size ?? '');
        $categories = is_array($request->category_category) ? implode(',', $request->category_category) : ($request->category_category ?? '');

        $product->update([
            'name' => $request->name,
            'color' => $request->color,
            'size' => $sizes,
            'image' => $imagePath,
            'category' => $categories,
            'price' => $request->price,
            'status' => $request->status == 'Active' ? 'Active' : 'InActive',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }

    /**
     * Delete a product (soft delete).
     */
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'Deleted']);

        return response()->json([
            'status' => true,
            'message' => 'Product deleted successfully'
        ], 200);
    }
}
