    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Category</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    </head>
    <body>
        <div class="container mt-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Create Category</h4>
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
                    <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <!--category--> 
                        <div class="mb-3">

                            <label for="validationcategory" class="form-label">category</label>
                            <input type="text" name="category" id="validationcategory" class="form-control is-valid"  value="{{ old('category') }}"  required>

                            <!-- Validation feedback -->
                            <div class="invalid-feedback">
            Please provide a category.
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
                        <a href="{{ route('category.index') }}" class="btn btn-primary"> Category Index</a>
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


    </body>
    </html>
