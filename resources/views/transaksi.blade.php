@extends('layouts.app')

@section('title', 'Daftar Transaksi')
@section('page_title', 'Transaksi')

@section('content')
<div class="grid grid-cols-1 gap-6">

    <!-- Form Tambah Transaksi -->
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="card-title"><i class="fa-solid fa-plus-circle text-primary"></i> Tambah Transaksi Baru</h2>
        </div>
        <div class="card-body">
            <!-- Alert success mapping from ScanStruk saving -->
            @if(session('success'))
                <div class="alert alert-success mb-4 text-sm p-4 bg-green-50 border border-green-200 text-green-700 rounded-md shadow-sm">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            <form id="formTransaksi" class="grid grid-cols-2 gap-4">
                <div class="form-group col-span-2 md:col-span-1">
                    <label class="form-label" for="judul">Judul Transaksi</label>
                    <input type="text" id="judul" class="form-control" placeholder="Contoh: Beli Kopi" required>
                </div>
                
                <div class="form-group col-span-2 md:col-span-1">
                    <label class="form-label" for="jumlah">Jumlah (Rp)</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 16px; top: 12px; color: var(--text-muted); font-weight: 500;">Rp</span>
                        <input type="number" id="jumlah" class="form-control" style="padding-left: 48px;" placeholder="0" min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tipe">Tipe</label>
                    <select id="tipe" class="form-control" required>
                        <option value="pengeluaran">Pengeluaran</option>
                        <option value="pemasukan">Pemasukan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" class="form-control" required>
                </div>

                <div class="form-group col-span-2">
                    <label class="form-label" for="kategori">Kategori</label>
                    <select id="kategori" class="form-control" required>
                        <option value="Makanan & Minuman">Makanan & Minuman</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Belanja">Belanja</option>
                        <option value="Tagihan">Tagihan & Utilitas</option>
                        <option value="Hiburan">Hiburan</option>
                        <option value="Gaji">Gaji</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="col-span-2 mt-2">
                    <button type="submit" class="btn btn-primary" id="btnSimpan" style="width: 100%; padding: 12px; font-weight: 600;">
                        <i class="fa-solid fa-save"></i> Simpan Transaksi
                    </button>
                    <p class="text-muted text-center mt-2" style="font-size: 0.75rem;">Disimpan ke Database</p>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="card">
        <div class="card-header flex justify-between items-center" style="border-bottom: 1px solid var(--border); padding-bottom: 16px;">
            <h2 class="card-title">Riwayat Transaksi</h2>
            <div class="flex gap-2">
                <select class="form-control" id="filterTipe" style="padding: 6px 12px; font-size: 0.875rem; height: auto;">
                    <option value="semua">Semua Tipe</option>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                </select>
                <select class="form-control" id="filterKategori" style="padding: 6px 12px; font-size: 0.875rem; height: auto;">
                    <option value="semua">Semua Kategori</option>
                </select>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50%">Detail</th>
                        <th>Kategori</th>
                        <th style="text-align: right;">Jumlah</th>
                        <th style="width: 60px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transactionsList">
                    <!-- Default Loading State -->
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                            <i class="fa-solid fa-circle-notch fa-spin text-primary" style="font-size: 2rem; margin-bottom: 16px; display: block;"></i>
                            Memuat data transaksi...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const API_URL = 'https://69ae8872c8b37f499835c282.mockapi.io/api/v1/transactions';
    let transactionsData = [];

    const DOM = {
        form: document.getElementById('formTransaksi'),
        list: document.getElementById('transactionsList'),
        filterTipe: document.getElementById('filterTipe'),
        filterKategori: document.getElementById('filterKategori'),
        btnSimpan: document.getElementById('btnSimpan')
    };

    // Initialize formatting and current date
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('tanggal').valueAsDate = new Date();
        loadTransactions();
        
        DOM.form.addEventListener('submit', handleAddTransaction);
        DOM.filterTipe.addEventListener('change', renderTransactions);
        DOM.filterKategori.addEventListener('change', renderTransactions);
    });

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function getCategoryIcon(type, category) {
        if (type === 'pemasukan') return '<i class="fa-solid fa-money-bill-wave text-success"></i>';
        
        const icons = {
            'Makanan & Minuman': '<i class="fa-solid fa-utensils text-danger"></i>',
            'Transportasi': '<i class="fa-solid fa-gas-pump text-primary"></i>',
            'Belanja': '<i class="fa-solid fa-cart-shopping text-warning"></i>',
            'Tagihan': '<i class="fa-solid fa-file-invoice text-danger"></i>',
            'Hiburan': '<i class="fa-solid fa-film text-purple"></i>',
            'Lainnya': '<i class="fa-solid fa-box text-muted"></i>'
        };
        return icons[category] || icons['Lainnya'];
    }

    async function loadTransactions() {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) throw new Error('Gagal mengambil data dari API');
            
            let allData = await response.json();
            // Filter by current user
            const currentUserId = getUserId();
            transactionsData = allData.filter(tx => tx.user_id === currentUserId);
            // Urutkan berdasarkan tanggal terbaru
            transactionsData.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
            
            updateKategoriDropdown();
            renderTransactions();
        } catch (error) {
            console.error('Error fetching transactions:', error);
            DOM.list.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: var(--danger);">
                        <i class="fa-solid fa-circle-exclamation mb-4" style="font-size: 2rem;"></i><br>
                        Gagal memuat data dari MockAPI.<br>Pastikan koneksi internet stabil.
                    </td>
                </tr>`;
        }
    }

    function updateKategoriDropdown() {
        // Ambil kategori unik yang ada di list transaksi jika belum ada di opsi default, tapi untuk sederhana kita biarkan default filter
    }

    function renderTransactions() {
        const filterT = DOM.filterTipe.value;
        const filterK = DOM.filterKategori.value;

        // Filter Data
        const filtered = transactionsData.filter(tx => {
            const matchTipe = filterT === 'semua' || tx.tipe === filterT;
            const matchKat = filterK === 'semua' || tx.kategori === filterK;
            return matchTipe && matchKat;
        });

        if (filtered.length === 0) {
            DOM.list.innerHTML = `
                <tr>
                    <td colspan="4" style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                        <div style="background: var(--bg-hover); width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i class="fa-solid fa-receipt" style="font-size: 1.5rem;"></i>
                        </div>
                        <p style="font-weight: 500;">Belum ada transaksi</p>
                        <p style="font-size: 0.875rem;">Mulai catat transaksi Anda hari ini.</p>
                    </td>
                </tr>`;
            return;
        }

        let html = '';
        filtered.forEach(tx => {
            const isExpense = tx.tipe === 'pengeluaran';
            const sign = isExpense ? '-' : '+';
            const amountColor = isExpense ? 'var(--danger)' : 'var(--success)';
            const bgIcon = isExpense ? 'var(--danger-bg)' : 'var(--success-bg)';
            
            const dateStr = new Date(tx.tanggal).toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric'
            });

            html += `
                <tr>
                    <td>
                        <div class="flex items-center gap-4">
                            <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: ${bgIcon}; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                ${getCategoryIcon(tx.tipe, tx.kategori)}
                            </div>
                            <div>
                                <div style="font-weight: 600; margin-bottom: 2px;">${tx.judul}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">${dateStr}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge ${isExpense ? 'badge-primary' : 'badge-success'}">${tx.kategori}</span></td>
                    <td style="text-align: right; color: ${amountColor}; font-weight: 700;">${sign} ${formatCurrency(tx.jumlah)}</td>
                    <td style="text-align: center;">
                        <button onclick="deleteTransaction('${tx.id}')" class="btn btn-outline text-danger" style="padding: 6px 12px; border-color: transparent;" title="Hapus">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        
        DOM.list.innerHTML = html;
    }

    async function handleAddTransaction(e) {
        e.preventDefault();
        
        // Simpan state asli tombol
        const originalBtnText = DOM.btnSimpan.innerHTML;
        DOM.btnSimpan.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        DOM.btnSimpan.disabled = true;

        const newData = {
            judul: document.getElementById('judul').value,
            jumlah: parseInt(document.getElementById('jumlah').value),
            tipe: document.getElementById('tipe').value,
            tanggal: document.getElementById('tanggal').value,
            kategori: document.getElementById('kategori').value,
            user_id: getUserId()
        };

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newData)
            });

            if (!response.ok) throw new Error('Gagal menyimpan transaksi');

            const savedTx = await response.json();
            
            // Tambahkan ke state lokal dan render ulang
            transactionsData.unshift(savedTx);
            renderTransactions();

            // Reset form
            DOM.form.reset();
            document.getElementById('tanggal').valueAsDate = new Date();
            
            // Tampilkan alert native JS
            alert("Berhasil menambahkan transaksi baru!");
            
        } catch (error) {
            console.error('Save Error:', error);
            alert('Gagal menyimpan otomatis ke MockAPI. Cek console.');
        } finally {
            // Restore button
            DOM.btnSimpan.innerHTML = originalBtnText;
            DOM.btnSimpan.disabled = false;
        }
    }

    async function deleteTransaction(id) {
        if (!confirm('Anda yakin ingin menghapus transaksi ini?')) return;

        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE'
            });

            if (!response.ok) throw new Error('Gagal menghapus');

            // Hapus dari state lokal
            transactionsData = transactionsData.filter(tx => tx.id !== id);
            renderTransactions();

        } catch (error) {
            console.error('Delete error:', error);
            alert('Gagal menghapus transaksi dari MockAPI.');
        }
    }
</script>
@endsection
