<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6, #ffffff);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-top: 50px;
        }
        h2 {
            color: #343a40;
            font-weight: 700;
        }
        .search-box {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .badge-color {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            color: #fff;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .img-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .btn-primary {
            border-radius: 20px;
            padding: 8px 20px;
        }
        .alert {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">🛒 Purchase History</h2>

    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control search-box" placeholder="Search by product name, category, size, or address ID">
    </div>

    <!-- Purchase Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle" id="purchaseTable">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>User ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Image</th>
                    <th>Address ID</th>
                    <th>Price (₹)</th>
                    <th>Tax (₹)</th>
                    <th>Total (₹)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $index => $order)
                    <tr>
                        <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}</td>
                        <td>{{ $order->user_id }}</td>
                        <td>{{ $order->product_name }}</td>
                        <td>{{ $order->categories ?? 'N/A' }}</td>
                        <td>{{ $order->sizes ?? 'N/A' }}</td>
                        <td>
                            @if($order->color)
                                <span class="badge-color" style="background-color: {{ $order->color }};">
                                    {{ ucfirst($order->color) }}
                                </span>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($order->image)
                                <img src="{{ asset('fresh_images/' . $order->image) }}" alt="Product Image" class="img-thumbnail">
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $order->address_id }}</td>
                        <td>₹{{ number_format($order->price, 2) }}</td>
                        <td>₹{{ number_format($order->tax, 2) }}</td>
                        <td>₹{{ number_format($order->total_price, 2) }}</td>
                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted">No purchase history found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links('pagination::bootstrap-4') }}
    </div>

    <!-- Back Button -->
    <div class="text-center mt-4">
        <a href="{{ route('product.index') }}" class="btn btn-primary">← Back to Products</a>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Search Filter -->
<script>
    document.getElementById('searchInput').addEventListener('keyup', function () {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#purchaseTable tbody tr');

        rows.forEach(row => {
            let productName = row.cells[2].innerText.toLowerCase();
            let category = row.cells[3].innerText.toLowerCase();
            let size = row.cells[4].innerText.toLowerCase();
            let addressId = row.cells[7].innerText.toLowerCase();

            if (
                productName.includes(filter) ||
                category.includes(filter) ||
                size.includes(filter) ||
                addressId.includes(filter)
            ) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
