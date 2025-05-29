<?php

namespace App\Http\Controllers\Api;
use App\Models\Cart;
use App\Models\customer;
use App\Models\category;
use App\Models\size;
use App\Models\product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // ✅ View Cart Items
    public function addToCart(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:customer,id', // ✅ Corrected table name
            'product_id' => 'required|exists:products,id',
            'size' => 'required|string',
            'color' => 'required|string',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'address_id' => 'required|exists:address,id' // ✅ Ensure address_id exists
        ]);

        // Fetch customer
        $customer = Customer::find($request->user_id);
        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'User not found.'], 404);
        }

        // Fetch product
        $product = Product::findOrFail($request->product_id);

        // Get size & category names
        $sizeName = Size::where('id', $request->size)->value('size');
        $categoryName = Category::where('id', $request->category)->value('category');

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads', 'public');
            $imageUrl = url('storage/' . $imagePath);
        } else {
            $imageUrl = $product->image ? url($product->image) : null;
        }

        // ✅ Store in Cart Table
        $cart = Cart::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'size' => $sizeName,
            'color' => $request->color,
            'category' => $categoryName,
            'price' => $product->price,
            'image' => $imageUrl,
            'address_id' => $request->address_id // ✅ Store selected address
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product added to cart successfully!',
            'cart' => $cart
        ], 201);
    }
public function index(Request $request)
{
    $cartItems = Cart::where('user_id', $request->user_id)
        ->with('product', 'address') // ✅ Include product & address details
        ->get();

    return response()->json([
        'status' => true,
        'cart' => $cartItems
    ], 200);
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
