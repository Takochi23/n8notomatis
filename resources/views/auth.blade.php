<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TakoSaving - {{ $mode === 'login' ? 'Masuk' : 'Daftar' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>

    <div class="auth-container">

        <a href="/" class="logo">
            <img src="{{ asset('takosaving.png') }}" alt="TakoSaving Logo" class="auth-logo-img">
            TakoSaving
        </a>

        <div class="auth-header">
            <h3>
                {{ $mode === 'login' ? 'Selamat Datang Kembali' : 'Daftar Akun Baru' }}
            </h3>

            <p>
                {{ $mode === 'login'
                    ? 'Masuk ke TakoSaving Anda'
                    : 'Bergabunglah untuk pantau keuangan lebih baik'
                }}
            </p>
        </div>

        <!-- Form -->
        <form id="loginForm">

            @if($mode === 'register')
            <div class="form-group">
                <label>Nama Lengkap</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-user"></i>

                    <input
                        type="text"
                        id="fullname"
                        placeholder="Masukkan nama Anda"
                        required
                    >
                </div>
            </div>
            @endif

            <!-- EMAIL -->
            <div class="form-group">
                <label>Email</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        placeholder="nama@email.com"
                        required
                    >
                </div>
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label>Password</label>

                <div class="input-wrapper">
                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        placeholder="••••••••"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn-submit">

                @if($mode === 'login')
                    Masuk
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                @else
                    Buat Akun Sekarang
                    <i class="fa-solid fa-user-plus"></i>
                @endif

            </button>

        </form>
        <div class="auth-footer">

            @if($mode === 'login')
                Belum daftar?
                <a href="{{ route('register') }}">
                    Buat akun gratis
                </a>
            @else
                Sudah punya akun?
                <a href="{{ route('login') }}">
                    Masuk di sini
                </a>
            @endif

        </div>

    </div>

<!-- Supabase -->
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

<script>
    const supabaseClient = window.supabase.createClient(
        "{{ config('services.supabase.url') }}",
        "{{ config('services.supabase.key') }}"
    );
</script>

<!-- Login -->
<script src="{{ asset('js/auth.js') }}"></script>

</body>
</html>