<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login — PayFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --dark: #0b1220;
            --muted: #6b7280;
            --mint: #34d399;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            height: 100vh;
            display: flex;
        }

        /* LEFT SIDE */
        .left-side {
            flex: 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 40px;
        }
        .left-side img {
            width: 450px;
            max-width: 90%;
        }

        /* RIGHT SIDE */
        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            background: linear-gradient(180deg, #0b1220, #072255);
        }

        /* LOGIN CARD */
        .card {
            border: none;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            padding: 30px 35px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .card-body {
            text-align: center;
        }

        .card-body h4 {
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 25px;
        }

        label {
            float: left;
            color: #e5e7eb;
            font-size: 14px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: var(--mint);
            box-shadow: 0 0 0 0.2rem rgba(52, 211, 153, 0.25);
            color: white;
        }

        .btn-primary {
            background-color: var(--mint);
            border: none;
            color: #0b1220;
            font-weight: 600;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #2fb98a;
        }

        a {
            color: var(--mint);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .form-check-label {
            color: #d1d5db;
        }

        @media (max-width: 900px) {
            body { flex-direction: column; }
            .left-side { height: 200px; }
            .right-side { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <div class="left-side">
        <img src="{{ asset('images/payflowlogo.png') }}" alt="PayFlow Logo">
    </div>

    <div class="right-side">
        <div class="card shadow-lg">
            <div class="card-body">
                <h4>Welcome Back</h4>

                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3 text-start">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 text-start">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3 form-check text-start">
                        <input type="checkbox" name="remember_token" id="remember_token" class="form-check-input">
                        <label class="form-check-label" for="remember_token">Remember me</label>
                    </div>

                    <div class="d-grid mb-3">
                        <button class="btn btn-primary btn-lg">Login</button>
                    </div>

                    <div>
                        <a href="{{ route('password.request') }}">Forgot your password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
