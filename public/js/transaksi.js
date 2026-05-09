let transactionsData = [];

const DOM = {
    form: document.getElementById('formTransaksi'),
    list: document.getElementById('transactionsList'),
    filterTipe: document.getElementById('filterTipe'),
    filterKategori: document.getElementById('filterKategori'),
    btnSimpan: document.getElementById('btnSimpan')
};

// Inisialisasi format dan tanggal saat ini
document.addEventListener('DOMContentLoaded', () => {
    const tanggalInput = document.getElementById('tanggal');
    if (tanggalInput) {
        tanggalInput.valueAsDate = new Date();
    }
    loadTransactions();
    
    if (DOM.form) DOM.form.addEventListener('submit', handleAddTransaction);
    if (DOM.filterTipe) DOM.filterTipe.addEventListener('change', renderTransactions);
    if (DOM.filterKategori) DOM.filterKategori.addEventListener('change', renderTransactions);
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
        if (typeof getUserId !== 'function') return;
        const currentUserId = getUserId();
        const response = await fetch(`/ajax/transactions?user_id=${encodeURIComponent(currentUserId)}`);
        if (!response.ok) throw new Error('Gagal mengambil data dari API');
        
        transactionsData = await response.json();
        // Urutkan berdasarkan tanggal terbaru
        transactionsData.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
        
        updateKategoriDropdown();
        renderTransactions();
    } catch (error) {
        console.error('Error fetching transactions:', error);
        if (DOM.list) {
            DOM.list.innerHTML = `
                <tr>
                    <td colspan="4" class="error-state">
                        <i class="fa-solid fa-circle-exclamation error-icon"></i><br>
                        Gagal memuat data dari database.<br>Pastikan koneksi internet stabil.
                    </td>
                </tr>`;
        }
    }
}

function updateKategoriDropdown() {
    // Logika untuk kategori dinamis dapat ditambahkan di sini
}

function renderTransactions() {
    if (!DOM.list) return;
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
                <td colspan="4" class="empty-state">
                    <div class="empty-icon-circle-lg">
                        <i class="fa-solid fa-receipt empty-icon"></i>
                    </div>
                    <p class="font-medium">Belum ada transaksi</p>
                    <p class="text-sm">Mulai catat transaksi Anda hari ini.</p>
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
                        <div class="tx-icon-container" style="background: ${bgIcon};">
                            ${getCategoryIcon(tx.tipe, tx.kategori)}
                        </div>
                        <div>
                            <div class="tx-title">${tx.judul}</div>
                            <div class="text-muted text-xs">${dateStr}</div>
                        </div>
                    </div>
                </td>
                <td><span class="badge ${isExpense ? 'badge-primary' : 'badge-success'}">${tx.kategori}</span></td>
                <td class="text-right tx-amount" style="color: ${amountColor};">
                    ${sign} ${formatCurrency(tx.jumlah)}
                </td>
                <td class="text-center">
                    <button onclick="deleteTransaction('${tx.id}')" class="btn btn-outline text-danger btn-delete-tx" title="Hapus">
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
    if (typeof CSRF_TOKEN === 'undefined') return;
    
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
        const response = await fetch('/ajax/transactions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
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
        alert('Gagal menyimpan transaksi. Cek console.');
    } finally {
        // Restore button
        DOM.btnSimpan.innerHTML = originalBtnText;
        DOM.btnSimpan.disabled = false;
    }
}

async function deleteTransaction(id) {
    if (!confirm('Anda yakin ingin menghapus transaksi ini?')) return;
    if (typeof CSRF_TOKEN === 'undefined') return;

    try {
        const response = await fetch(`/ajax/transactions/${id}?user_id=${encodeURIComponent(getUserId())}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN
            }
        });

        if (!response.ok) throw new Error('Gagal menghapus');

        // Hapus dari state lokal
        transactionsData = transactionsData.filter(tx => tx.id !== id);
        renderTransactions();

    } catch (error) {
        console.error('Delete error:', error);
        alert('Gagal menghapus transaksi.');
    }
}
