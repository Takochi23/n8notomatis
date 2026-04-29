@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="grid grid-cols-4 gap-6 mb-6">
    <!-- Stat 1 -->
    <div class="card stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-wallet"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Saldo</div>
            <div class="stat-value" id="total-saldo">Rp 0</div>
        </div>
    </div>
    
    <!-- Stat 2 -->
    <div class="card stat-card">
        <div class="stat-icon success">
            <i class="fa-solid fa-arrow-down"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pemasukan Bulan Ini</div>
            <div class="stat-value" id="total-pemasukan">Rp 0</div>
        </div>
    </div>
    
    <!-- Stat 3 -->
    <div class="card stat-card">
        <div class="stat-icon danger">
            <i class="fa-solid fa-arrow-up"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Pengeluaran Bulan Ini</div>
            <div class="stat-value" id="total-pengeluaran">Rp 0</div>
        </div>
    </div>

    <!-- Stat 4 -->
    <div class="card stat-card">
        <div class="stat-icon" style="background-color: var(--warning-bg); color: var(--warning);">
            <i class="fa-solid fa-piggy-bank"></i>
        </div>
        <div class="stat-info">
            <div class="stat-label">Tabungan</div>
            <div class="stat-value" id="total-tabungan">Rp 0</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 gap-6">
    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Transaksi Terakhir</h2>
            <a href="/transaksi" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.75rem;">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th style="text-align: right;">Jumlah</th>
                    </tr>
                </thead>
                <tbody id="recent-transactions">
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: var(--text-muted);">
                            <i class="fa-solid fa-circle-notch fa-spin text-primary" style="margin-right: 8px;"></i> Memuat data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Action / Menu -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Aksi Cepat</h2>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-2 gap-4">
                <a href="/scanstruk" class="btn btn-outline" style="flex-direction: column; height: auto; padding: 24px; border-style: dashed; border-width: 2px;">
                    <i class="fa-solid fa-camera" style="font-size: 2rem; color: var(--primary); margin-bottom: 12px;"></i>
                    <span style="font-size: 1rem; font-weight: 600;">Scan Struk AI</span>
                    <span class="text-muted" style="font-size: 0.75rem; text-align: center; margin-top: 4px;">Catat belanja secara otoamtis</span>
                </a>
                <a href="/transaksi" class="btn btn-outline" style="flex-direction: column; height: auto; padding: 24px; border-style: dashed; border-width: 2px;">
                    <i class="fa-solid fa-file-invoice-dollar" style="font-size: 2rem; color: var(--success); margin-bottom: 12px;"></i>
                    <span style="font-size: 1rem; font-weight: 600;">Catat Manual</span>
                    <span class="text-muted" style="font-size: 0.75rem; text-align: center; margin-top: 4px;">Tambahkan secara manual</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const currentUserId = getUserId();
            const resp = await fetch(`/ajax/transactions?user_id=${encodeURIComponent(currentUserId)}`);
            if(resp.ok) {
                let data = await resp.json();
                
                // Sort by relative recent
                data.sort((a,b) => new Date(b.tanggal) - new Date(a.tanggal));

                let totalIn = 0;
                let totalOut = 0;

                data.forEach(tx => {
                    const amt = parseFloat(tx.jumlah);
                    if(tx.tipe === 'pengeluaran') totalOut += amt;
                    else totalIn += amt;
                });

                const saldo = totalIn - totalOut;
                const tabungan = saldo > 0 ? saldo * 0.2 : 0; // Asumsi 20% simpanan

                document.getElementById('total-saldo').innerText = formatCurrency(saldo);
                document.getElementById('total-pemasukan').innerText = formatCurrency(totalIn);
                document.getElementById('total-pengeluaran').innerText = formatCurrency(totalOut);
                document.getElementById('total-tabungan').innerText = formatCurrency(tabungan);

                // Render latest 5 tx
                renderRecentTable(data.slice(0, 5));
            }
        } catch(e) {
            console.error(e);
            document.getElementById('recent-transactions').innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: var(--danger);">
                        Gagal memuat data dari database.
                    </td>
                </tr>`;
        }
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(amount);
    }

    function getCategoryIcon(type, category) {
        if (type === 'pemasukan') return '<i class="fa-solid fa-money-bill-wave"></i>';
        
        const icons = {
            'Makanan & Minuman': '<i class="fa-solid fa-utensils"></i>',
            'Transportasi': '<i class="fa-solid fa-gas-pump"></i>',
            'Belanja': '<i class="fa-solid fa-cart-shopping"></i>',
            'Tagihan': '<i class="fa-solid fa-file-invoice"></i>',
            'Hiburan': '<i class="fa-solid fa-film"></i>',
            'Lainnya': '<i class="fa-solid fa-box"></i>'
        };
        return icons[category] || icons['Lainnya'];
    }

    function getCategoryClass(type, category) {
        if (type === 'pemasukan') return 'success';
        
        const classes = {
            'Makanan & Minuman': 'danger',
            'Transportasi': 'primary',
            'Belanja': 'warning',
            'Tagihan': 'danger',
            'Hiburan': 'primary',
            'Lainnya': 'muted'
        };
        return classes[category] || 'muted';
    }

    function renderRecentTable(data) {
        const tbody = document.getElementById('recent-transactions');
        if(data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">Belum ada transaksi</td></tr>';
            return;
        }

        let html = '';
        data.forEach(tx => {
            const isOut = tx.tipe === 'pengeluaran';
            const sign = isOut ? '-' : '+';
            const amountColor = isOut ? 'var(--danger)' : 'var(--success)';
            const cls = getCategoryClass(tx.tipe, tx.kategori);
            
            const dateStr = new Date(tx.tanggal).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'short', year: 'numeric'
            });

            html += `
                <tr>
                    <td>
                        <div class="flex items-center gap-4">
                            <div class="stat-icon ${cls}" style="width:36px; height:36px; font-size:1rem;">
                                ${getCategoryIcon(tx.tipe, tx.kategori)}
                            </div>
                            <span style="font-weight: 500;">${tx.judul}</span>
                        </div>
                    </td>
                    <td><span class="badge badge-primary" style="background-color: var(--${cls}-bg); color: var(--${cls}); border: none;">${tx.kategori}</span></td>
                    <td><span class="text-muted">${dateStr}</span></td>
                    <td style="text-align: right; color: ${amountColor}; font-weight: 600;">${sign} ${formatCurrency(tx.jumlah)}</td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }
</script>
@endsection
