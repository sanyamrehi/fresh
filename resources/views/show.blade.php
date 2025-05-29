<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .product-box {
            max-width: 900px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .product-box h2 {
            font-weight: 700;
            margin-bottom: 30px;
        }

        .product-image img {
            max-width: 120px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .badge-color {
            padding: 5px 12px;
            border-radius: 20px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            text-transform: capitalize;
        }

        .price-box {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-top: 30px;
        }

        .price-box h5 {
            margin: 8px 0;
            font-weight: 600;
        }

        .price-value {
            font-size: 18px;
            color: #198754;
        }

        .btn-lg {
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="product-box">
        <h2 class="text-center">Product Details</h2>

        @if($products)
            <div class="row g-4 align-items-center">
                <div class="col-md-4 text-center product-image">
                    @if($products->image && file_exists(public_path('/fresh_images/' . $products->image)))
                        <img src="{{ asset('/fresh_images/' . $products->image) }}" alt="Product Image">
                    @else
                        <p class="text-muted">No image available</p>
                    @endif
                </div>

                <div class="col-md-8">
                    <table class="table borderless">
                        <tbody>
                            <tr>
                                <td class="info-label">Bill Index No:</td>
                                <td class="info-value">{{ $products->id }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Product Name:</td>
                                <td class="info-value">{{ $products->name }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Category:</td>
                                <td class="info-value">{{ $products->categories ?? 'Uncategorized' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Size:</td>
                                <td class="info-value">{{ $products->sizes ?? 'Not specified' }}</td>
                            </tr>
                            <tr>
                                <td class="info-label">Color:</td>
                                <td class="info-value">
                                    @if($products->color)
                                        <span class="badge-color" style="background-color: {{ $products->color }}; color: {{ $products->color == 'white' ? '#000' : '#fff' }};">
                                            {{ $products->color }}
                                        </span>
                                    @else
                                        <span class="text-muted">No color specified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="info-label">Address ID:</td>
                                <td class="info-value">{{ $selectedAddressId ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Price Calculation -->
            <div class="price-box mt-4">
                @php
                    $taxRate = 0.16;
                    $taxAmount = $products->price * $taxRate;
                    $totalPrice = $products->price + $taxAmount;
                @endphp

                <h5>Base Price: <span class="price-value">${{ number_format($products->price, 2) }}</span></h5>
                <h5>Tax (16%): <span class="price-value">${{ number_format($taxAmount, 2) }}</span></h5>
                <h5>Total Amount: <span class="price-value">${{ number_format($totalPrice, 2) }}</span></h5>
            </div>

            <!-- Buttons -->
            <form action="{{ route('checkout.store', ['id' => $products->id]) }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="address_id" value="{{ request('address_id', session('selected_address_id')) }}">
                <div class="d-grid gap-3">
                    <button type="submit" class="btn btn-primary btn-lg">Proceed to Checkout</button>
                    <button type="button" id="pay-button" class="btn btn-success btn-lg">Pay Now</button>
                </div>
            </form>
        @else
            <div class="alert alert-warning text-center">No product details found.</div>
        @endif
    </div>
</div>

<!-- Bootstrap and Razorpay -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('pay-button').onclick = function (e) {
        e.preventDefault();

        const options = {
            key: "{{ env('RAZORPAY_KEY') }}",
            amount: "{{ $totalPrice * 100 }}",
            currency: "INR",
            name: "{{ $products->name }}",
            description: "Product Purchase",
            image: "{{ asset('/fresh_images/' . $products->image) }}",
            order_id: "{{ $razorpayOrderId ?? '' }}",
            handler: function (response) {
                fetch("{{ route('razorpay.payment') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature,
                        product_id: "{{ $products->id }}",
                        address_id: "{{ request('address_id', session('selected_address_id')) }}"
                    })
                }).then(res => res.json())
                  .then(data => {
                      if (data.success) {
                          window.location.href = "/order/success";
                      }
                  });
            },
            prefill: {
                name: "{{ auth()->user()->name ?? 'Guest' }}",
                email: "{{ auth()->user()->email ?? 'example@example.com' }}"
            },
            theme: {
                color: "#3399cc"
            }
        };

        const rzp1 = new Razorpay(options);
        rzp1.open();
    };
</script>

</body>
</html>
