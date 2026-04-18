<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TakoSaving') - Aplikasi Keuangan</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    @stack('styles')
</head>
<body>

    <!-- Checkbox Hack for CSS-only Mobile Sidebar Toggle -->
    <input type="checkbox" id="mobile-menu-toggle">
    
    <!-- Overlay for mobile closing -->
    <label for="mobile-menu-toggle" class="menu-overlay"></label>

    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="/dashboard" class="sidebar-logo">
                <img src="{{ asset('takosaving.png') }}" alt="TakoSaving Logo" style="width: 46px; height: 40px; border-radius: 8px; object-fit: cover;">
                    TakoSaving
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                        <a href="/dashboard" class="nav-link">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('transaksi') ? 'active' : '' }}">
                        <a href="/transaksi" class="nav-link">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                            <span>Transaksi</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('analitik') ? 'active' : '' }}">
                        <a href="/analitik" class="nav-link">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Analitik</span>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->is('scanstruk') ? 'active' : '' }}">
                        <a href="/scanstruk" class="nav-link">
                            <i class="fa-solid fa-camera"></i>
                            <span>Scan Struk</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar" id="sidebar-avatar">
                        U
                    </div>
                    <div class="user-info">
                        <span class="user-name" id="sidebar-name">User</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <label for="mobile-menu-toggle" class="mobile-toggle-btn">
                        <i class="fa-solid fa-bars"></i>
                    </label>
                    <h1 class="page-title">@yield('page_title', 'Dashboard')</h1>
                </div>
                <div class="topbar-right">
                    <a href="/transaksi" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Tambah Transaksi
                    </a>
                </div>
            </header>

            <main class="content">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const storedName = localStorage.getItem('takosaving_user');
            if (storedName) {
                const nameEl = document.getElementById('sidebar-name');
                const avatarEl = document.getElementById('sidebar-avatar');
                if(nameEl) nameEl.textContent = storedName;
                if(avatarEl) avatarEl.textContent = storedName.charAt(0).toUpperCase();
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
