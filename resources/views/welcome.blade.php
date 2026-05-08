<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TakoSaving - Kelola Keuangan dengan Cerdas</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>
    <nav>

        <a href="/" class="logo">
            <img src="{{ asset('takosaving.png') }}" alt="TakoSaving Logo" class="logo-img">
            TakoSaving
        </a>

        <div class="auth-buttons" id="authButtons">

            <!-- Button Default -->
            <a href="{{ route('login') }}" class="welcome-btn welcome-btn-outline">
                Log in
            </a>

            <a href="{{ route('register') }}" class="welcome-btn welcome-btn-black">
                Daftar Gratis
            </a>

        </div>

    </nav>

    <main>
        <section class="hero">

            <h1>
                Cerdas Mengelola <br>
                <span class="accent-blue">Keuangan Pribadi</span>
            </h1>

            <p>
                Aplikasi pelacak keuangan minimalis.
                Catat manual atau gunakan AI via n8n untuk otomatis
                ekstrak data dari struk belanja Anda.
            </p>

            <a href="{{ route('register') }}"
               class="welcome-btn welcome-btn-black hero-cta"
               id="heroButton">

                Mulai Sekarang - Gratis

            </a>

        </section>
        <section class="features">

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-camera"></i>
                </div>

                <h3>Scan Struk AI</h3>

                <p>
                    Otomatisasi pengisian transaksi dengan memfoto struk kasir.
                    Didukung oleh integrasi n8n webhook dan Gemini Vision AI
                    untuk ekstraksi akurat.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="color: #000;">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>

                <h3>Analisis Mendalam</h3>

                <p>
                    Visualisasi interaktif arus kas dan pengeluaran per kategori.
                    Pahami ke mana uang Anda pergi setiap bulannya dengan mudah.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="color: #10b981;">
                    <i class="fa-solid fa-cloud"></i>
                </div>

                <h3>Sinkronisasi Cloud Database</h3>

                <p>
                    Data tersimpan dengan aman di cloud database Supabase.
                    Tidak ada lagi kekhawatiran data hilang saat berganti
                    browser atau perangkat lokal.
                </p>
            </div>

        </section>

    </main>

    <!-- Login Cek -->
    <script>

        document.addEventListener('DOMContentLoaded', () => {

            const user = localStorage.getItem('takosaving_user_id');

            const authButtons = document.getElementById('authButtons');
            const heroButton = document.getElementById('heroButton');

            // JIKA SUDAH LOGIN
            if(user){

                authButtons.innerHTML = `
                    <a href="/dashboard" class="welcome-btn welcome-btn-black">
                        Ke Dashboard
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                `;

                heroButton.innerHTML = `
                    Ke Dashboard
                `;

                heroButton.href = "/dashboard";
            }

        });

    </script>

</body>
</html>