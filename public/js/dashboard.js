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
        const recentTransactions = document.getElementById('recent-transactions');
        if (recentTransactions) {
            recentTransactions.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center" style="padding: 20px; color: var(--danger);">
                        Gagal memuat data dari database.
                    </td>
                </tr>`;
        }
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
    if(!tbody) return;
    
    if(data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="loading-state">Belum ada transaksi</td></tr>';
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
