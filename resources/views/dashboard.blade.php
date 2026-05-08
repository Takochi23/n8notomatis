@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<div class="grid grid-cols-4 gap-6 mb-6">
    <!-- Stat 1 -->
    <div class="card stat-card">
        <div class="stat-icon icon-primary">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Saldo</div>
            <div class="stat-value" id="total-saldo">Rp 0</div>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="card stat-card">
        <div class="stat-icon icon-success">
            <i class="fa-solid fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pemasukan Bulan Ini</div>
            <div class="stat-value" id="total-pemasukan">Rp 0</div>
        </div>
    </div>
    
    <!-- Stat 3 -->
    <div class="card stat-card">
        <div class="stat-icon icon-danger">
            <i class="fa-solid fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pengeluaran Bulan Ini</div>
            <div class="stat-value" id="total-pengeluaran">Rp 0</div>
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="card stat-card" id="spending-status-card">
        <div class="stat-icon icon-warning" id="spending-status-icon">
            <i class="fa-solid fa-fire" id="spending-status-fa"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pengeluaran Hari Ini</div>
            <div class="stat-value" id="total-hari-ini">Rp 0</div>
            <div class="spending-status-label" id="spending-status-label">💤 Belum ada</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-6">
    <!-- Transaksi -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Transaksi Terakhir</h2>
            <a href="/transaksi" class="btn btn-outline btn-small">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody id="recent-transactions">
                    <tr>
                        <td colspan="4" class="loading-state">
                            <i class="fa-solid fa-circle-notch fa-spin text-primary mr-2"></i> Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Menu -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Aksi Cepat</h2>
        </div>
        <div class="card-body">
            <div class="quick-action-grid">
                <a href="/scanstruk" class="quick-action-btn">
                    <i class="fa-solid fa-camera quick-action-icon color-primary"></i>
                    <span class="quick-action-label">Scan Struk AI</span>
                    <span class="quick-action-desc">Catat belanja secara otoamtis</span>
                </a>
                <a href="/transaksi" class="quick-action-btn">
                    <i class="fa-solid fa-file-invoice-dollar quick-action-icon color-success"></i>
                    <span class="quick-action-label">Catat Manual</span>
                    <span class="quick-action-desc">Tambahkan secara manual</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/dashboard.js') }}"></script>
@endsection

@section('scripts')

<script>

document.addEventListener('DOMContentLoaded', () => {

    const isLoggedIn = localStorage.getItem('isLoggedIn');

    if(!isLoggedIn){
        window.location.href = "/login";
    }

});

</script>

<script src="{{ asset('js/dashboard.js') }}"></script>

@endsection
