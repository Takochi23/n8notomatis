let txData = [];
let catChart;
let trChart;
let currentDays = 7; // Default filter

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
        const currentUserId = getUserId();
        const resp = await fetch(`/ajax/transactions?user_id=${encodeURIComponent(currentUserId)}`);
        if(resp.ok) {
            txData = await resp.json();
            processData();
        }
    } catch (e) {
        console.error('Error fetching data for analytics:', e);
    }

    // Time filter tab click handlers
    document.querySelectorAll('.time-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.time-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentDays = parseInt(btn.dataset.days);
            processData();
        });
    });
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
    }).format(amount);
}

function getFilteredData() {
    const now = new Date();
    // Start of today
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());

    let startDate;
    if (currentDays === 1) {
        startDate = today;
    } else {
        startDate = new Date(today);
        startDate.setDate(startDate.getDate() - (currentDays - 1));
    }

    return txData.filter(tx => {
        const txDate = new Date(tx.tanggal);
        return txDate >= startDate && txDate <= now;
    });
}

function processData() {
    const filtered = getFilteredData();

    let income = 0;
    let expense = 0;
    const categoryMap = {};
    const trendMap = {};

    filtered.forEach(tx => {
        const amount = parseFloat(tx.jumlah);
        const isExpense = tx.tipe === 'pengeluaran';
        const dateStr = tx.tanggal.substring(0, 10);

        if (isExpense) {
            expense += amount;
            if(!categoryMap[tx.kategori]) categoryMap[tx.kategori] = 0;
            categoryMap[tx.kategori] += amount;
        } else {
            income += amount;
        }

        if(!trendMap[dateStr]) trendMap[dateStr] = { income: 0, expense: 0 };
        if(isExpense) {
            trendMap[dateStr].expense += amount;
        } else {
            trendMap[dateStr].income += amount;
        }
    });

    // Update Summary DOM
    const incomeEl = document.getElementById('summary-income');
    const expenseEl = document.getElementById('summary-expense');
    const balanceEl = document.getElementById('summary-balance');
    
    if (incomeEl) incomeEl.innerText = formatCurrency(income);
    if (expenseEl) expenseEl.innerText = formatCurrency(expense);
    if (balanceEl) balanceEl.innerText = formatCurrency(income - expense);

    renderCategoryChart(categoryMap, expense);
    renderTrendChart(trendMap);
}

function renderCategoryChart(categoryMap, totalExpense) {
    const labels = Object.keys(categoryMap);
    const data = Object.values(categoryMap);
    
    const sorted = labels.map((l, i) => ({ label: l, value: data[i] }))
                         .sort((a,b) => b.value - a.value);

    const canvas = document.getElementById('categoryChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    if(data.length === 0) {
        if (catChart) catChart.destroy();
        catChart = null;
        const legendEl = document.getElementById('category-legend');
        if (legendEl) legendEl.innerHTML = '<p class="text-center text-muted" style="padding: 20px;">Belum ada data pengeluaran di periode ini.</p>';
    } else {
        if (catChart) catChart.destroy();
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
                    legend: { display: false }
                }
            }
        });

        let legHtml = '<div class="grid grid-cols-2 gap-4">';
        sorted.forEach((item, i) => {
            const perc = totalExpense > 0 ? ((item.value / totalExpense) * 100).toFixed(1) : 0;
            legHtml += `
                <div class="legend-item">
                    <div class="flex items-center gap-3">
                        <span class="legend-color-dot" style="background:${COLORS[i % COLORS.length]}"></span>
                        <span class="legend-label">${item.label}</span>
                    </div>
                    <div class="legend-value-container">
                        <div class="legend-percentage">${perc}%</div>
                        <div class="legend-amount">${formatCurrency(item.value)}</div>
                    </div>
                </div>
            `;
        });
        legHtml += '</div>';
        const legendEl = document.getElementById('category-legend');
        if (legendEl) legendEl.innerHTML = legHtml;
    }
}

function renderTrendChart(trendMap) {
    // Fill in missing dates for the selected period
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    let startDate;
    if (currentDays === 1) {
        startDate = new Date(today);
    } else {
        startDate = new Date(today);
        startDate.setDate(startDate.getDate() - (currentDays - 1));
    }

    const allDates = [];
    const d = new Date(startDate);
    while (d <= today) {
        allDates.push(d.toISOString().substring(0, 10));
        d.setDate(d.getDate() + 1);
    }

    const labels = allDates.map(dateStr => {
        const dt = new Date(dateStr);
        return dt.toLocaleDateString('id-ID', {day: 'numeric', month:'short'});
    });
    const incomeData = allDates.map(d => (trendMap[d] ? trendMap[d].income : 0));
    const expenseData = allDates.map(d => (trendMap[d] ? trendMap[d].expense : 0));

    const canvas = document.getElementById('trendChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    if (trChart) trChart.destroy();
    trChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: incomeData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderRadius: 4
                },
                {
                    label: 'Pengeluaran',
                    data: expenseData,
                    backgroundColor: '#000000',
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
