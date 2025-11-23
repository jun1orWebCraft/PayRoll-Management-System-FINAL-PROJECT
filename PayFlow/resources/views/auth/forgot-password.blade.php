<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password — PayFlow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --accent-color: #34d399;
            --dark-bg: #0b1220;
            --muted-color: #6b7280;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1f2a44, #0b1220);
        }

        .forgot-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 40px 35px;
            max-width: 480px;
            width: 100%;
            color: white;
            box-shadow: 0 12px 25px rgba(0,0,0,0.45);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .forgot-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px rgba(0,0,0,0.55);
        }

        .forgot-card h4 {
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: #ffffff;
        }

        label {
            color: #e5e7eb;
            font-weight: 500;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 211, 153, 0.25);
            color: white;
        }

        .btn-primary, .btn-success {
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
        }

        .btn-primary:hover {
            background-color: #2fb98a;
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: #22c55e;
            border: none;
        }

        .btn-success:hover {
            background-color: #16a34a;
            transform: translateY(-2px);
        }

        .alert-success {
            background-color: rgba(52, 211, 153, 0.15);
            border-color: rgba(52, 211, 153, 0.25);
            color: #bbf7d0;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.15);
            border-color: rgba(220, 53, 69, 0.25);
            color: #f8d7da;
        }

        a {
            color: var(--accent-color);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #2fb98a;
            text-decoration: underline;
        }

        input.text-center {
            letter-spacing: 0.3em;
        }

        @media (max-width: 600px) {
            .forgot-card {
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>

<div class="forgot-card">
    <h4>Forgot Password</h4>

    @if(session('status'))
        <div class="alert alert-success">{!! session('status') !!}</div>
    @endif

    @if(!session('show_code_field'))
        {{-- Step 1: Enter email --}}
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="example@gmail.com" required autofocus>
                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="d-grid mb-3">
                <button class="btn btn-primary btn-lg">Send Verification Code</button>
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
            <div class="d-grid mb-3">
                <button class="btn btn-success btn-lg">Verify Code</button>
            </div>
        </form>
    @endif

    <div class="text-center mt-2">
        <a href="{{ route('login') }}">Back to login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
