<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f6f9;
        }
        .login-card {
            background: #fff;
            width: 100%;
            max-width: 380px;
            padding: 2rem 2rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .login-card h1 {
            font-size: 1.4rem;
            margin: 0 0 1.5rem;
            text-align: center;
        }
        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.35rem;
            color: #333;
        }
        .field input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d7dbe0;
            border-radius: 6px;
            font-size: 0.95rem;
        }
        .field input:focus {
            outline: none;
            border-color: #4f46e5;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }
        .btn-submit {
            width: 100%;
            padding: 0.7rem;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-submit:hover { background: #4338ca; }
        .errors {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 0.6rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .back-home {
            display: block;
            text-align: center;
            margin-top: 1rem;
            font-size: 0.85rem;
            color: #6b7280;
            text-decoration: none;
        }
        .back-home:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-card">
        <h1>Masuk ke Admin</h1>

        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <div class="field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="remember">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin:0;font-weight:400;">Ingat saya</label>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <a href="{{ route('home') }}" class="back-home">&larr; Kembali ke beranda</a>
    </div>
</body>
</html>
