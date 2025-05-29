<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Update Profile</h4>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="card-body">
                <!-- Update Form -->
                <form action="{{ route('products.update', ['id' => $products['id']]) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               placeholder="Enter name" value="{{ old('name', $products['name']) }}" required>
                        <div class="invalid-feedback">
                        @if ($errors->has('name'))
    <div class="alert alert-danger">
        {{ $errors->first('name') }}
    </div>
@endif
                        </div>
                    </div>

                    <!-- Color -->
                    <div class="mb-3">
                        <label for="color" class="form-label">Color</label>
                        <input type="color" name="color" id="colorPicker" class="form-control form-control-color"
                               value="{{ old('color', $products['color']) }}" required>
                        <div class="invalid-feedback">
                            Please choose a color.
                        </div>
                    </div>

                    <!-- Size -->
<div class="form-group">
    <label for="size" class="form-label">Size</label>
    <select id="size" name="size_size[]" class="form-control" multiple required>
        @foreach($sizes as $size)
            <option value="{{ $size->id }}"
                @if(isset($selectedSizes) && is_array($selectedSizes) && in_array($size->id, $selectedSizes))
                    selected
                @endif>
                {{ $size->size }}
            </option>
        @endforeach
    </select>
    <div class="invalid-feedback">
        Please select a size.
    </div>
</div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" name="image" id="image" class="form-control" onchange="previewImage(event)">
                        <img src="{{ asset('fresh_images/' . $products['image']) }}" id="imagePreview" alt="Current Image" width="100" class="mt-2">
                        <input type="hidden" name="hdnimage" value="{{ $products['image'] }}">
                        <div class="invalid-feedback">Please choose a valid image.</div>
                    </div>

                    <!-- Category -->
<div class="form-group">
    <label for="category">Category</label>
    <select id="category" name="category_category[]" class="form-control" multiple required>
        @foreach($categories as $category)
            <option value="{{ $category->id }}"
                @if(isset($selectedCategories) && is_array($selectedCategories) && in_array($category->id, $selectedCategories))
                    selected
                @endif>
                {{ $category->category }}
            </option>
        @endforeach
    </select>
</div>

                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="text" name="price" id="price" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $products['price']) }}" pattern="\d{1,2}\.?\d{1,2}" required>
                        <div class="invalid-feedback">
                            @error('price') {{ $message }} @else Please provide a valid price (e.g., 100.00). @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-check mb-3">
                        <input type="checkbox" name="status" id="status" class="form-check-input" value="Active"
                               {{ old('status', $products['status'] ?? '') == 'Active' ? 'checked' : '' }}>
                        <label for="status" class="form-check-label">Active</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success">Save</button>
                    <a href="{{ route('admin.showadmin') }}" class="btn btn-primary">Back to Index</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview selected image
        function previewImage(event) {
            const image = document.getElementById('imagePreview');
            image.src = URL.createObjectURL(event.target.files[0]);
            image.style.display = 'block';
        }

        // Initialize Select2
        $(document).ready(function() {
        $('.select2').select2();
        let selectedCategories = @json(old('category_id', $selectedCategory ?? []));
        $('#category').val(selectedCategories).trigger('change');
    });

    </script>

</body>
</html>
