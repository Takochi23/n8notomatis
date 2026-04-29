@extends('layouts.app')

@section('title', 'Analitik')
@section('page_title', 'Laporan & Insight')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/analitik.css') }}">
@endpush

@section('content')
<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="card analytics-hero">
        <div class="card-body analytics-hero-body">
            <div class="grid grid-cols-3 gap-6 text-center">
                <div class="border-r-light">
                    <p class="analytics-stat-label">Sisa Uang</p>
                    <h2 id="summary-balance" class="analytics-stat-value-large">Rp 0</h2>
                </div>
                <div class="border-r-light">
                    <p class="analytics-stat-label">Pemasukan</p>
                    <h2 id="summary-income" class="analytics-stat-value" style="color: var(--success);">Rp 0</h2>
                </div>
                <div>
                    <p class="analytics-stat-label">Pengeluaran</p>
                    <h2 id="summary-expense" class="analytics-stat-value" style="color: var(--danger);">Rp 0</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-6">
    <!-- Chart Kategori -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-chart-pie text-primary"></i> Pengeluaran per Kategori</h2>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="categoryChart"></canvas>
            </div>
            
            <div id="category-legend" class="mt-6">
                <!-- Akan diisi JS -->
            </div>
        </div>
    </div>

    <!-- Chart Tren Bulanan -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-chart-line text-primary"></i> Tren Keuangan 30 Hari</h2>
        </div>
        <div class="card-body">
            <div class="chart-container" style="width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('scripts')
<script src="{{ asset('js/analitik.js') }}"></script>
@endsection

