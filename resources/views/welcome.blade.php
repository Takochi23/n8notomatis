<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TakoSaving - Kelola Keuangan dengan Cerdas</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-body: #ffffff;
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
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Navigation */
        nav {
            padding: 24px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--black);
            text-decoration: none;
        }
        
        .auth-buttons {
            display: flex;
            gap: 16px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-outline {
            border: 2px solid var(--text-muted);
            color: var(--text-main);
        }
        
        .btn-outline:hover {
            border-color: var(--black);
        }
        
        .btn-black {
            background-color: var(--black);
            color: white;
        }
        
        .btn-black:hover {
            background-color: #333;
            transform: translateY(-2px);
        }
        
        /* Hero Section */
        .hero {
            text-align: center;
            padding: 80px 5%;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 4rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -1px;
        }
        
        .hero p {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 40px;
            max-width: 600px;
            margin-inline: auto;
        }
        
        .accent-blue {
            color: var(--primary);
        }
        
        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 32px;
            padding: 0 5% 80px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .feature-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 32px;
            border-radius: 16px;
            transition: transform 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
        }
        
        .feature-icon {
            width: 48px;
            height: 48px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 20px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        
        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 12px;
        }
        
        .feature-card p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>

    <nav>
        <a href="/" class="logo">
            <img src="{{ asset('takosaving.png') }}" alt="TakoSaving Logo" style="width: 56px; height: 56px; border-radius: 12px; object-fit: cover;">
            TakoSaving
        </a>
        <div class="auth-buttons">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-black">Ke Dashboard <i class="fa-solid fa-arrow-right"></i></a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-black">Daftar Gratis</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <main>
        <section class="hero">
            <h1>Cerdas Mengelola <br><span class="accent-blue">Keuangan Pribadi</span></h1>
            <p>Aplikasi pelacak keuangan minimalis. Catat manual atau gunakan AI via n8n untuk otomatis ekstrak data dari struk belanja Anda.</p>
            @if (!Auth::check())
                <a href="{{ route('register') }}" class="btn btn-black" style="padding: 16px 32px; font-size: 1.1rem;">
                    Mulai Sekarang - Gratis
                </a>
            @endif
        </section>

        <section class="features">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-camera"></i></div>
                <h3>Scan Struk AI</h3>
                <p>Otomatisasi pengisian transaksi dengan memfoto struk kasir. Didukung oleh integrasi n8n webhook dan Gemini Vision AI untuk ekstraksi akurat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="color: #000;"><i class="fa-solid fa-chart-pie"></i></div>
                <h3>Analisis Mendalam</h3>
                <p>Visualisasi interaktif arus kas dan pengeluaran per kategori. Pahami ke mana uang Anda pergi setiap bulannya dengan mudah.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="color: #10b981;"><i class="fa-solid fa-cloud"></i></div>
                <h3>Sinkronisasi MockAPI</h3>
                <p>Data tersimpan dengan aman menggunakan MockAPI. Tidak ada lagi kekhawatiran data hilang saat berganti browser atau perangkat lokal.</p>
            </div>
        </section>
    </main>

</body>
</html>
