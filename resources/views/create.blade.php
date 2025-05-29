<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Create Profile</h4>
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
            <!--create form-->
                <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <!-- name -->
                    <div class="mb-3">
                        <label for="validationname" class="form-label">name</label>
                        <input type="text" name="name" id="name" class="form-control is-valid" placeholder="Enter name" value="{{ old('name') }}"  required>
                        <!--validation feedback-->
                        <div class="invalid-feedback">
                            Please provide a unique name.
                        </div>
                    </div>

                    <!--color label-->
                    <div class="mb-3">

                        <label for="color" class="form-label">color</label>
                        <input type="color" name="color" id="colorPicker" class="form-control form-control-color" value="{{ old('color') }}"  required>

                        <!--validation feedback-->
                        <div class="invalid-feedback">
                            Please choose a  color.
                        </div>
                    </div>
                    <!-- size -->
                    <div class="form-group">
                        <label for="size" >size</label>
                        <select name="size_size[]" id="size" class="form-select form-control" multiple="multiple" required>
                        <option value="">Select size</option>
                        @foreach($sizes as $size)
                        <option value="{{ $size->id }}" @if(in_array($size->id,
     request()->get('sizes')??[]))selected="selected"
    @endif>{{$size->size}}</option>
                        @endforeach
                        </select>
                        <!--validation feedback-->
                        <div class="invalid-feedback">
        Please provide a valid size .
    </div>
                    </div>
                    <!-- Image -->
                    <div class="mb-3">
                        <label for="image" class="form-label">Image:</label>
                        <input type="file" name="image" id="image" class="form-control" onchange="previewImage(event)" value="{{ old('image') }}" required>
                        <img id="preview" class="img-thumbnail mt-2" style="max-width: 150px; display: none;">
                        <!--validation feedback-->
                        <div class="invalid-feedback">please provide a valid image </div>
                    </div>
                    <!--category-->
                    <div class="form-group">
                        <label for="category" >category</label>
                        <select name="category_category[]" id="category" class="form-select form-control" multiple="multiple" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" @if(in_array($category->id,
     request()->get('categories')??[]))selected="selected"
    @endif>{{$category->category}}</option>
                        @endforeach
                        </select>
                        <!--validation feedback-->
                        <div class="invalid-feedback">
        Please provide a valid category .
    </div>
                    </div>

                    <!-- price -->
                    <div class="mb-3">

                        <label for="validationprice" class="form-label">price</label>
                        <input type="text" name="price" id="validationprice" class="form-control is-valid" pattern="\d{1,2}\.?\d{1,2}"  value="{{ old('price') }}"  required>

                        <!-- Validation feedback -->
                         <div class="invalid-feedback">
        Please provide a valid price (e.g., 100.00 or 99.99).
    </div>
                    </div>


                    <!--check box-->
                    <div class="form-control">
                        <input type="checkbox" name="status" value="Active" {{ old('status' ?? '') == 'Active' }}>
                        <label>Active</label><br /><br />
                    </div>
    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success">Save</button>
                    <!--Index page -->
                    <a href="{{ route('admin.showadmin') }}" class="btn btn-primary">Index</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('validationphone').addEventListener('input', function() {
            var phoneInput = document.getElementById('validationphone');//checks the phone is valid
            var feedback = phoneInput.nextElementSibling; // Get the invalid-feedback div

            // Check if the phone number matches the pattern
            if (phoneInput.validity.patternMismatch) {
                phoneInput.classList.add('is-invalid'); // Show invalid feedback
                feedback.style.display = 'block'; // Show the invalid-feedback
            } else {
                phoneInput.classList.remove('is-invalid'); // Hide invalid feedback
                feedback.style.display = 'none'; // Hide the invalid-feedback
            }
        });

    </script>
    <script>
        (function() {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        //image display after selecting
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('preview');
                preview.src = reader.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function validateImage(event) {
            const fileInput = event.target;
            const file = fileInput.files[0];
            const preview = document.getElementById("preview");
            const feedback = document.getElementById("imageFeedback");

            // Reset validation feedback
            fileInput.classList.remove("is-invalid");
            feedback.textContent = "";
            preview.style.display = "none";

            if (file) {
                // Validate file type
                const validTypes = ["image/jpeg", "image/png", "image/gif"];
                if (!validTypes.includes(file.type)) {
                    fileInput.classList.add("is-invalid");
                    feedback.textContent = "Please upload a valid image file (JPEG, PNG, or GIF).";
                    return;
                }

                // Validate file size (e.g., max 2MB)
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    fileInput.classList.add("is-invalid");
                    feedback.textContent = "File size must not exceed 2MB.";
                    return;
                }

                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";
                };
                reader.readAsDataURL(file);
            }
        }

    </script>
</body>
</html>
