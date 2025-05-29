<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e3f2fd);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 1rem;
        }

        .table thead {
            background-color: #007bff;
            color: #fff;
        }

        .btn-success, .btn-primary {
            border-radius: 25px;
        }

        .btn-warning, .btn-danger {
            border-radius: 8px;
        }

        .pagination .page-link {
            border-radius: 0.5rem;
            color: #007bff;
        }

        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
        }

        .form-control {
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="card p-4">
        <h1 class="text-center mb-4 text-primary">Product List</h1>

        <div class="row mb-4">
            <div class="col-md-8 mb-3 mb-md-0">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by name, category, size, or price">
            </div>
            <div class="col-md-4">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <a href="{{ route('size.index') }}" class="btn btn-success">Size Index</a>
                    <a href="{{ route('category.index') }}" class="btn btn-success">Category Index</a>
                    <a href="{{ route('product.create') }}" class="btn btn-success">Add Product</a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="mb-3 d-flex gap-2">
            @csrf
            <input type="file" name="file" required class="form-control w-auto">
            <button type="submit" class="btn btn-primary">Import CSV</button>
            <a href="{{ route('admin.products.export') }}" class="btn btn-primary">Export CSV</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="productTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Color</th>
                    <th>Size</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <!-- Dynamic rows -->
                </tbody>
            </table>
        </div>

        <nav>
            <ul class="pagination justify-content-center mt-3" id="paginationLinks">
                <!-- Pagination links -->
            </ul>
        </nav>

        <div class="text-center mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        function fetchProducts(search = '', page = 1) {
            $.ajax({
                url: '{{ route('product.index') }}',
                type: 'GET',
                data: { search: search, page: page },
                success: function (response) {
                    const tableBody = $('#productTable tbody');
                    const paginationLinks = $('#paginationLinks');

                    let index = (response.current_page - 1) * response.per_page + 1;

                    tableBody.empty();
                    paginationLinks.empty();

                    response.data.forEach((product) => {
                        tableBody.append(`
                            <tr>
                                <td>${index++}</td>
                                <td>${product.name}</td>
                                <td style="background-color: ${product.color};">${product.color}</td>
                                <td>${product.sizes}</td>
                                <td><img src="/fresh_images/${product.image}" class="img-thumbnail" style="width: 100px;" alt="Product Image"></td>
                                <td>${product.categories}</td>
                                <td>${product.price}</td>
                                <td>${product.status}</td>
                                <td>
                                    <a href="admin/product/edit/${product.id}" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="admin/product/delete/${product.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        `);
                    });

                    paginationLinks.html(response.links);
                },
                error: function () {
                    alert("Failed to fetch data. Please try again later.");
                }
            });
        }

        fetchProducts();

        $('#searchInput').on('keyup', debounce(function () {
            const searchQuery = $(this).val();
            fetchProducts(searchQuery);
        }, 300));

        $(document).on('click', '#paginationLinks a', function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const page = new URL(url).searchParams.get('page') || 1;
            fetchProducts(new URL(url).searchParams.get('search') || '', page);
        });
    });
</script>
</body>
</html>
