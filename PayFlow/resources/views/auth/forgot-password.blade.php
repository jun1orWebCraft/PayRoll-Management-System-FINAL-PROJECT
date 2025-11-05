<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Forgot Password — PayFlow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h4 class="mb-3">Forgot Password</h4>

                    @if(session('status'))
                        <div class="alert alert-success">{!! session('status') !!}</div>
                    @endif

                    @if(!session('show_code_field'))
                        {{-- Step 1: Enter email --}}
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3 text-start">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary">Send Verification Code</button>
                            </div>
                        </form>
                    @else
                        {{-- Step 2: Enter verification code --}}
                        <form method="POST" action="{{ route('password.verify') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('email') }}">
                            <div class="mb-3">
                                <label class="form-label">Enter 6-digit code</label>
                                <input type="text" name="code" maxlength="6" class="form-control text-center fs-4" required>
                                @error('code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-success">Verify Code</button>
                            </div>
                        </form>
                    @endif

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
