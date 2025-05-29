<?php

namespace App\Http\Controllers\Api;
use App\Models\Cart;
use App\Models\order;
use App\Models\product;
use App\Models\size;
use App\Models\category;
use App\Models\customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request)
    {
        // ✅ Validate Input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'size' => 'array', // Accept array of IDs (Optional)
            'size.*' => 'integer|exists:sizes,id',
            'size_names' => 'array', // Accept array of names (Optional)
            'size_names.*' => 'string',
            'category' => 'array', // Accept array of IDs (Optional)
            'category.*' => 'integer|exists:categories,id',
            'category_names' => 'array', // Accept array of names (Optional)
            'category_names.*' => 'string',
            'address_id' => 'required|exists:address,id'
        ]);

        // ✅ Fetch Product Details
        $product = Product::findOrFail($request->product_id);

        // ✅ Handle Sizes (Insert new ones if needed)
        $sizeIds = $request->size ?? [];
        if ($request->has('size_names')) {
            foreach ($request->size_names as $sizeName) {
                $size = Size::firstOrCreate(['size' => $sizeName]); // Insert if not exists
                $sizeIds[] = $size->id;
            }
        }
        $sizes = Size::whereIn('id', $sizeIds)->get(['id', 'size']);

        // ✅ Handle Categories (Insert new ones if needed)
        $categoryIds = $request->category ?? [];
        if ($request->has('category_names')) {
            foreach ($request->category_names as $categoryName) {
                $category = Category::firstOrCreate(['category' => $categoryName]); // Insert if not exists
                $categoryIds[] = $category->id;
            }
        }
        $categories = Category::whereIn('id', $categoryIds)->get(['id', 'category']);

        // ✅ Calculate Tax & Total Price
        $taxRate = config('app.tax_rate', 0.16); // Default 16% tax
        $taxAmount = $product->price * $taxRate;
        $totalPrice = $product->price + $taxAmount;

        // ✅ Store Order
        $order = Order::create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'product_name' => $product->name,
            'size' => implode(',', $sizeIds),
            'category' => implode(',', $categoryIds),
            'price' => $product->price,
            'tax' => $taxAmount,
            'total_price' => $totalPrice,
            'address_id' => $request->address_id,
            'status' => 'Pending'
        ]);

        // ✅ Return Response with IDs & Names
        return response()->json([
            'status' => true,
            'message' => 'Order placed successfully!',
            'order' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'product_id' => $order->product_id,
                'product_name' => $order->product_name,
                'sizes' => $sizes,
                'categories' => $categories,
                'price' => $order->price,
                'tax' => $order->tax,
                'total_price' => $order->total_price,
                'address_id' => $order->address_id,
                'status' => $order->status,
            ]
        ], 201);
    }

     /**
     * Get the order history for the authenticated user.
     */
    public function index(Request $request)
    {
        $customerId = $request->input('user_id');

        if (!$customerId) {
            return response()->json([
                'status' => false,
                'message' => 'User ID is required.'
            ], 400);
        }

        $query = Order::select(
                'orders.*',
                'products.id as product_id',
                'products.name as product_name',
                'products.color',
                'products.image',
                'address.address',
                'address.city',
                'address.state',
                'address.pincode'
            )
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->leftJoin('address', 'orders.address_id', '=', 'address.id')
            ->where('orders.user_id', $customerId);

        // ✅ Optional filters
        if ($request->filled('status')) {
            $query->where('orders.status', $request->input('status'));
        }

        if ($request->filled('product_name')) {
            $query->where('products.name', 'LIKE', '%' . $request->input('product_name') . '%');
        }

        if ($request->filled('city')) {
            $query->where('address.city', 'LIKE', '%' . $request->input('city') . '%');
        }

        // ✅ Sort by price if provided
        if ($request->filled('filter')) {
            $sort = strtolower($request->input('filter'));
            if (in_array($sort, ['asc', 'desc'])) {
                $query->orderBy('orders.price', $sort);
            }
        }

        $orders = $query->get()->map(function ($order) {
            $sizeIds = explode(',', $order->size_ids ?? '');
            $categoryIds = explode(',', $order->category_ids ?? '');

            $sizes = Size::whereIn('id', $sizeIds)->get(['id', 'size']);
            $categories = Category::whereIn('id', $categoryIds)->get(['id', 'category']);

            return [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'product' => [
                    'id' => $order->product_id,
                    'name' => $order->product_name
                ],
                'sizes' => $sizes->map(fn($s) => ['id' => $s->id, 'name' => $s->size]),
                'categories' => $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->category]),
                'color' => $order->color,
                'image' => $order->image,
                'price' => $order->price,
                'tax' => $order->tax,
                'total_price' => $order->total_price,
                'address' => $order->address,
                'city' => $order->city,
                'state' => $order->state,
                'pincode' => $order->pincode,
                'status' => $order->status,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Orders retrieved successfully!',
            'orders' => $orders
        ], 200);
    }

}
