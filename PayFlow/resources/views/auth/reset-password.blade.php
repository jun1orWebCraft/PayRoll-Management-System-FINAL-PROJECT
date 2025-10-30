<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password — PayFlow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-3">Reset Password</h4>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus class="form-control">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" required class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="password_confirmation" required class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            Reset Password
                        </button>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('login') }}">Back to login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>