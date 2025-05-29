<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container my-4">
    <h1 class="text-center mb-4">Category List</h1>
 <div class="col-md-8">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by category">
        </div>
    <!-- Search Bar and Add Category Button -->
    <div class="row mb-3">
        <div class="col-md-4 text-end">
            <a href="{{ route('category.create') }}" class="btn btn-success">Add Category</a>
        </div>
    </div>

    <!-- Product Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle" id="categoryTable">
            <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <!-- Dynamic rows will be populated here -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center mt-3" id="paginationLinks">
            <!-- Pagination links will be dynamically added here -->
        </ul>
    </nav>

    <!-- Back to Dashboard Button -->
    <div class="text-center mt-4">
        <a href="{{ route('admin.showadmin') }}" class="btn btn-primary">Back to Product Index</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        // Debounce function to limit AJAX calls during typing
        function debounce(func, delay) {
            let timer;
            return function (...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Function to fetch products based on search query and page number
        function fetchProducts(search = '', page = 1) {
            $.ajax({
                url: '{{ route('category.index') }}',
                type: 'GET',
                data: { search: search, page: page },
                success: function (response) {
                    const tableBody = $('#categoryTable tbody');
                    const paginationLinks = $('#paginationLinks');
                    let index = (response.current_page - 1) * response.per_page + 1;

                    // Clear current content
                    tableBody.empty();
                    paginationLinks.empty();

                    // Populate table rows with product data
                    response.data.forEach((category) => {
                        tableBody.append(`
                            <tr>
                                <td>${index++}</td>
                                <td>${category.category}</td>
                                <td>${category.status}</td>
                                <td>
                                    <a href="/admin/category/edit/${category.id}" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="/admin/category/delete/${category.id}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        `);
                    });

                    // Pagination links
                    paginationLinks.html(response.links);
                },
                error: function () {
                    alert("Failed to fetch data. Please try again later.");
                }
            });
        }

        // Initial fetch for products
        fetchProducts();

        // Search input event (debounced)
        $('#searchInput').on('keyup', debounce(function () {
            const searchQuery = $(this).val();
            fetchProducts(searchQuery);
        }, 300));

        // Pagination link clicks
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
