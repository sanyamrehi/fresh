<?php

namespace App\Http\Controllers;
use App\Mail\OrderConfirmationMail;
use App\Models\customer;
use App\Models\order;
use App\Models\Address;
use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class orderController extends Controller
{
    public function store(Request $request, $id)
{
    $request->validate([
        'address_id' => 'required|exists:address,id',
    ]);

    $customerId = auth()->id();

    if (!$customerId) {
        return back()->with('error', 'You must be logged in to place an order.');
    }

    $product = Product::findOrFail($id);
    $address = Address::findOrFail($request->address_id);

    $taxRate = 0.16;
    $taxAmount = $product->price * $taxRate;
    $totalPrice = $product->price + $taxAmount;

    $order = Order::create([
        'user_id' => $customerId,
        'address_id' => $address->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'price' => $product->price,
        'tax' => $taxAmount,
        'total_price' => $totalPrice,
    ]);

    // ✅ Get customer from the `customers` table
    $customer = Customer::find($customerId);

    if ($customer && $customer->email) {
        Mail::to($customer->email)->send(new OrderConfirmationMail($order, $product, $address));
    }

    return redirect()->route('order.index')->with('success', 'Order placed and confirmation email sent!');
}

    public function index(Request $request)
    {
        $customerId = auth()->id();

        // Retrieve selected address ID from URL parameters or session
        $selectedAddressId = $request->query('address_id', session('selected_address_id'));

        // Store selected address in session
        session(['selected_address_id' => $selectedAddressId]);

        // Get user addresses
        $addresses = Address::where('user_id', $customerId)->get();

        // Fetch orders with address details (Paginate with 10 per page)
        $orders = Order::select(
                'orders.*',
                'orders.address_id',
                'products.size',
                'products.category',
                'products.color',
                'products.image',
                'address.address',
                'address.city',
                'address.state',
                'address.pincode'
            )
            ->leftJoin('products', 'orders.product_id', '=', 'products.id')
            ->leftJoin('address', 'orders.address_id', '=', 'address.id')
            ->selectRaw('(SELECT GROUP_CONCAT(sizes.size) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) as sizes')
            ->selectRaw('(SELECT GROUP_CONCAT(categories.category) FROM categories WHERE FIND_IN_SET(categories.id, products.category)) as categories')
            ->where('orders.user_id', $customerId)
            ->latest()
            ->paginate(10); // ✅ Use pagination

            // dd($selectedAddressId);

        return view('order.index', compact('orders', 'addresses', 'selectedAddressId'));
    }


}
