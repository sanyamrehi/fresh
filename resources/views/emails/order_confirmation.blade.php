<h2>Order Confirmation</h2>
<p>Hi {{ auth()->user()->name }},</p>

<p>Thank you for purchasing <strong>{{ $product->name }}</strong>!</p>

<ul>
    <li><strong>Category:</strong> {{ $product->categories ?? 'N/A' }}</li>
    <li><strong>Size:</strong> {{ $product->sizes ?? 'N/A' }}</li>
    <li><strong>Color:</strong> {{ ucfirst($product->color) ?? 'N/A' }}</li>
    <li><strong>Price:</strong> ₹{{ number_format($product->price, 2) }}</li>
    <li><strong>Tax:</strong> ₹{{ number_format($order->tax, 2) }}</li>
    <li><strong>Total:</strong> ₹{{ number_format($order->total_price, 2) }}</li>
</ul>

<h4>Delivery Address</h4>
<p>
    {{ $address->address }}, {{ $address->city }}<br>
    {{ $address->state }} - {{ $address->pincode }}
</p>

<p>We'll notify you when your order is shipped.</p>
