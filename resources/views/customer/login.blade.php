<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .login-container {
            max-width: 450px;
            margin: auto;
            margin-top: 60px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .social-btn {
            width: 100%;
            margin-bottom: 10px;
        }
        .social-btn.google {
            background-color: #dd4b39;
            color: white;
        }
        .social-btn.facebook {
            background-color: #3b5998;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h3 class="text-center mb-4">Customer Login</h3>
            <form action="{{ url('customer/loginform') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                    @if ($errors->has('password'))
                        <div class="text-danger mt-1">{{ $errors->first('password') }}</div>
                    @endif
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>

                <p class="text-center mb-2">Don't have an account? <a href="{{ url('customer/register') }}">Register here</a></p>
            </form>

            <hr>

            <div class="text-center mb-2">Or login with</div>

            <a href="{{ url('auth/google') }}" class="btn social-btn google">
                <i class="bi bi-google me-2"></i> Login with Google
            </a>

            <a href="{{ url('auth/facebook') }}" class="btn social-btn facebook">
                <i class="bi bi-facebook me-2"></i> Login with Facebook
            </a>
        </div>
    </div>

    <!-- Bootstrap Icons for Google/Facebook (optional, looks cleaner) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
