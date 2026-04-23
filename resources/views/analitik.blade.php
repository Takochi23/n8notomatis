@extends('layouts.app')

@section('title', 'Analitik')
@section('page_title', 'Laporan & Insight')

@section('content')
<div class="grid grid-cols-1 gap-6 mb-6">
    <div class="card bg-black text-white" style="background-image: linear-gradient(135deg, #111111ff 0%, #222 100%);">
        <div class="card-body" style="padding: 32px 24px;">
            <div class="grid grid-cols-3 gap-6 text-center">
                <div style="border-right: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-muted" style="color: #999; font-size: 0.875rem; margin-bottom: 4px;">Sisa Uang</p>
                    <h2 id="summary-balance" style="font-size: 1.75rem; font-weight: 800; color: white;">Rp 0</h2>
                </div>
                <div style="border-right: 1px solid rgba(255,255,255,0.1);">
                    <p class="text-muted" style="color: #999; font-size: 0.875rem; margin-bottom: 4px;">Pemasukan</p>
                    <h2 id="summary-income" style="font-size: 1.5rem; font-weight: 700; color: var(--success);">Rp 0</h2>
                </div>
                <div>
                    <p class="text-muted" style="color: #999; font-size: 0.875rem; margin-bottom: 4px;">Pengeluaran</p>
                    <h2 id="summary-expense" style="font-size: 1.5rem; font-weight: 700; color: var(--danger);">Rp 0</h2>
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
            <div style="height: 300px; display: flex; align-items: center; justify-content: center; position: relative;">
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
        <div class="card-body" style="padding: 24px;">
            <div style="height: 300px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('scripts')
<script>
    const API_URL = 'https://69ae8872c8b37f499835c282.mockapi.io/api/v1/transactions';
    let txData = [];
    let catChart;
    let trChart;

    const COLORS = [
        '#000000ff', // Black
        '#2563eb', // Blue Primary
        '#ef4444', // Red
        '#10b981', // Green
        '#8b5cf6', // Purple
        '#f59e0b', // Yellow
        '#64748b'  // Slate
    ];

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const resp = await fetch(API_URL);
            if(resp.ok) {
                let allData = await resp.json();
                // Filter by current user
                const currentUserId = getUserId();
                txData = allData.filter(tx => tx.user_id === currentUserId);
                processData();
            }
        } catch (e) {
            console.error('Error fetching data for analytics:', e);
        }
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(amount);
    }

    function processData() {
        let income = 0;
        let expense = 0;
        const categoryMap = {};
        
        // Data untrend chart
        const trendMap = {};

        txData.forEach(tx => {
            const amount = parseFloat(tx.jumlah);
            const isExpense = tx.tipe === 'pengeluaran';
            const dateStr = tx.tanggal.substring(0, 10); // YYYY-MM-DD

            // Summary
            if (isExpense) {
                expense += amount;
                
                // Kategori agregasi (hanya pengeluaran)
                if(!categoryMap[tx.kategori]) categoryMap[tx.kategori] = 0;
                categoryMap[tx.kategori] += amount;
            } else {
                income += amount;
            }

            // Tren harian
            if(!trendMap[dateStr]) trendMap[dateStr] = { income: 0, expense: 0 };
            if(isExpense) {
                trendMap[dateStr].expense += amount;
            } else {
                trendMap[dateStr].income += amount;
            }
        });

        // Update Summary DOM
        document.getElementById('summary-income').innerText = formatCurrency(income);
        document.getElementById('summary-expense').innerText = formatCurrency(expense);
        document.getElementById('summary-balance').innerText = formatCurrency(income - expense);

        renderCategoryChart(categoryMap, expense);
        renderTrendChart(trendMap);
    }

    function renderCategoryChart(categoryMap, totalExpense) {
        const labels = Object.keys(categoryMap);
        const data = Object.values(categoryMap);
        
        // Urutkan nilai terbanyak
        const sorted = labels.map((l, i) => ({ label: l, value: data[i] }))
                             .sort((a,b) => b.value - a.value);

        const ctx = document.getElementById('categoryChart').getContext('2d');
        
        if(data.length === 0) {
            // Placeholder empty
        } else {
            catChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: sorted.map(i => i.label),
                    datasets: [{
                        data: sorted.map(i => i.value),
                        backgroundColor: COLORS,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false } // Custom legend
                    }
                }
            });

            // Build custom legend
            let legHtml = '<div class="grid grid-cols-2 gap-4">';
            sorted.forEach((item, i) => {
                const perc = ((item.value / totalExpense) * 100).toFixed(1);
                legHtml += `
                    <div class="flex items-center justify-between p-3 rounded-md" style="background: var(--bg-hover);">
                        <div class="flex items-center gap-3">
                            <span style="display:block; width:12px; height:12px; border-radius:50%; background:${COLORS[i % COLORS.length]}"></span>
                            <span style="font-weight: 500; font-size: 0.875rem;">${item.label}</span>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; font-size: 0.875rem;">${perc}%</div>
                            <div class="text-muted" style="font-size: 0.75rem;">${formatCurrency(item.value)}</div>
                        </div>
                    </div>
                `;
            });
            legHtml += '</div>';
            document.getElementById('category-legend').innerHTML = legHtml;
        }
    }

    function renderTrendChart(trendMap) {
        // Sort tanggal
        const dates = Object.keys(trendMap).sort((a, b) => new Date(a) - new Date(b));
        // Jika data kurang 7 hari, Generate hari kosong - kita skip logic kompleks ini for simplicity
        
        const labels = dates.map(d => {
            const dt = new Date(d);
            return dt.toLocaleDateString('id-ID', {day: 'numeric', month:'short'});
        });
        const incomeData = dates.map(d => trendMap[d].income);
        const expenseData = dates.map(d => trendMap[d].expense);

        const ctx = document.getElementById('trendChart').getContext('2d');
        
        trChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: incomeData,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)', // Success green
                        borderRadius: 4
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseData,
                        backgroundColor: '#000000', // Black for expense
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8 }
                    }
                }
            }
        });
    }
</script>
@endsection
