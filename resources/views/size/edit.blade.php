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
                <form action="{{ route('size.update',['id' => $sizes['id']]) }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    <!--size-->
                    <div class="mb-3">

                        <label for="validationsize" class="form-label">size</label>
                        <input type="text" name="size" id="validationsize" class="form-control is-valid" value="{{ $sizes['size'] }}" required>

                        <!-- Validation feedback -->
                         <div class="invalid-feedback">
        Please provide a size.
    </div>
                    </div>

                    <!--check box-->
                    <div class="form-control">
                        <input type="checkbox" name="status"value="Active" {{ old('status', $sizes->status ?? '') == 'Active' ? 'checked' : '' }}>
                        <label>Active</label><br /><br />
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success">Save</button>
                    <!--Index page -->
                    <a href="{{ route('size.index') }}" class="btn btn-primary">Index</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
