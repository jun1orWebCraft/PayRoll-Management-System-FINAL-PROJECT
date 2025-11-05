<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Verify Code — PayFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
    <div class="container text-center">
        <div class="card shadow-sm mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h4 class="mb-3">Enter Verification Code</h4>

                @if(session('status'))
                    <div class="alert alert-success small">{!! session('status') !!}</div>
                @endif

                <form method="POST" action="{{ route('password.verify.code') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div class="mb-3">
                        <label class="form-label">6-Digit Code</label>
                        <input type="text" name="code" class="form-control text-center" maxlength="6" required autofocus>
                        @error('code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid">
                        <button class="btn btn-primary">Verify</button>
                    </div>
                </form>

                <div class="mt-3">
                    <a href="{{ route('password.request') }}">Back to Forgot Password</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
