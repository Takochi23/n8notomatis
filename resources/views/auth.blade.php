<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TakoSaving - {{ $mode === 'login' ? 'Masuk' : 'Daftar' }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-body: #f9fafb;
            --text-main: #111827;
            --text-muted: #6b7280;
            --primary: #2563eb;
            --black: #000000;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border: 1px solid #f3f4f6;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--black);
            text-align: center;
            margin-bottom: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-header h3 {
            font-size: 1.5rem;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .auth-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .input-wrapper input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
            outline: none;
        }

        .input-wrapper input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: var(--black);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .btn-submit:hover {
            background-color: #333;
            transform: translateY(-1px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="auth-container">
        <a href="/" class="logo">
        <img src="/takosaving.png"alt="TakoSaving Logo" style="width: 56px; height: 56px; border-radius: 12px; object-fit: cover;">
            TakoSaving
        </a>

        <div class="auth-header">
            <h3>{{ $mode === 'login' ? 'Selamat Datang Kembali ' : 'Daftar Akun Baru ' }}</h3>
            <p>{{ $mode === 'login' ? 'Masuk ke TakoSaving Anda' : 'Bergabunglah untuk pantau keuangan lebih baik' }}</p>
        </div>

        <form action="{{ route('dashboard') }}" method="GET" id="loginForm">
            @if($mode === 'register')
            <div class="form-group">
                <label>Nama Lengkap</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" placeholder="Masukkan nama Anda" required>
                </div>
            </div>
            @endif

            <div class="form-group">
                <label>Email</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" placeholder="nama@email.com" required>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                @if($mode === 'login')
                    Masuk <i class="fa-solid fa-arrow-right-to-bracket"></i>
                @else
                    Buat Akun Sekarang <i class="fa-solid fa-user-plus"></i>
                @endif
            </button>
        </form>

        <div class="auth-footer">
            @if($mode === 'login')
                Belum daftar? <a href="{{ route('register') }}">Buat akun gratis</a>
            @else
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            @endif
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        if(form) {
            form.addEventListener('submit', function() {
                const email = document.getElementById('email').value;
                const name = email.split('@')[0];
                const capitalizedName = name.charAt(0).toUpperCase() + name.slice(1);
                localStorage.setItem('takosaving_user', capitalizedName);
            });
        }
    </script>
</body>
</html>
