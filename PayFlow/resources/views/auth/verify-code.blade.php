<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Code — PayFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .verify-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 40px 35px;
            max-width: 400px;
            width: 100%;
            color: white;
            box-shadow: 0 12px 25px rgba(0,0,0,0.45);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .verify-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 35px rgba(0,0,0,0.55);
        }

        .verify-card h4 {
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

        .form-control.text-center {
            letter-spacing: 0.3em;
            font-size: 1.5rem;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border: none;
            font-weight: 600;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #2fb98a;
            transform: translateY(-2px);
        }

        .alert-success {
            background-color: rgba(52, 211, 153, 0.15);
            border-color: rgba(52, 211, 153, 0.25);
            color: #bbf7d0;
            border-radius: 8px;
        }

        .text-center a {
            color: var(--accent-color);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #2fb98a;
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .verify-card {
                padding: 30px 25px;
            }
            .form-control.text-center {
                font-size: 1.3rem;
                letter-spacing: 0.2em;
            }
        }
    </style>
</head>
<body>

<div class="verify-card">
    <h4>Enter Verification Code</h4>

    @if(session('status'))
        <div class="alert alert-success small">{!! session('status') !!}</div>
    @endif

    <form method="POST" action="{{ route('password.verify.code') }}">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-4">
            <label class="form-label">6-Digit Code</label>
            <input type="text" name="code" class="form-control text-center" maxlength="6" required autofocus>
            @error('code') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="d-grid mb-3">
            <button class="btn btn-primary btn-lg">Verify</button>
        </div>
    </form>

    <div class="text-center mt-2">
        <a href="{{ route('password.request') }}">Back to Forgot Password</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
