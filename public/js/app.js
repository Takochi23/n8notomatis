/**
 * FinKu - Dashboard JavaScript
 * Handles sidebar toggle, theme switching, and chart rendering.
 */

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initThemeToggle();
    initAnimations();
    loadDashboardData();
});

const API_URL = '/api/transactions';

async function loadDashboardData() {
    try {
        const response = await fetch(API_URL);
        if (response.ok) {
            const transactions = await response.json();
            calculateDashboardData(transactions);
            initCharts();
        }
    } catch (err) {
        console.error("Gagal mengambil data dari database", err);
    }
}

function calculateDashboardData(transactions) {
    let totalPemasukan = 0;
    let totalPengeluaran = 0;
    let perCategory = {
        makanan: 0, transport: 0, belanja: 0, tagihan: 0, hiburan: 0,
        kesehatan: 0, pendidikan: 0, gaji: 0, freelance: 0, investasi: 0, lainnya: 0
    };

    const monthlyIncome = { 0:0, 1:0, 2:0, 3:0, 4:0, 5:0 };
    const monthlyExpense = { 0:0, 1:0, 2:0, 3:0, 4:0, 5:0 };
    
    const now = new Date();
    const currentMonth = now.getMonth();

    transactions.forEach(tx => {
        const amt = Number(tx.jumlah) || 0;
        if (tx.tipe === 'pemasukan') {
            totalPemasukan += amt;
        } else {
            totalPengeluaran += amt;
            const cat = tx.kategori || 'lainnya';
            if(perCategory[cat] !== undefined) {
                perCategory[cat] += amt;
            } else {
                perCategory.lainnya += amt;
            }
        }

        // Monthly data 
        if(tx.tanggal) {
            const txDate = new Date(tx.tanggal);
            if(txDate.getFullYear() === now.getFullYear()) {
                const diff = currentMonth - txDate.getMonth();
                if(diff >= 0 && diff < 6) {
                    if(tx.tipe === 'pemasukan') monthlyIncome[5 - diff] += amt;
                    else monthlyExpense[5 - diff] += amt;
                }
            }
        }
    });

    const saldo = totalPemasukan - totalPengeluaran;

    // Update UI Cards
    const formatRp = (val) => 'Rp ' + val.toLocaleString('id-ID');
    const saldoEl = document.querySelector('.balance-card .card-value');
    const incomeEl = document.querySelector('.income-card .card-value');
    const expenseEl = document.querySelector('.expense-card .card-value');
    
    if (saldoEl) saldoEl.textContent = formatRp(saldo);
    if (incomeEl) incomeEl.textContent = formatRp(totalPemasukan);
    if (expenseEl) expenseEl.textContent = formatRp(totalPengeluaran);

    // Prepare chart data
    window.incomeData = Object.values(monthlyIncome);
    window.expenseData = Object.values(monthlyExpense);
    
    const catLabels = ['Belanja', 'Makanan', 'Transport', 'Tagihan', 'Hiburan', 'Lainnya'];
    const catAmts = [
        perCategory.belanja, 
        perCategory.makanan, 
        perCategory.transport, 
        perCategory.tagihan, 
        perCategory.hiburan, 
        perCategory.kesehatan + perCategory.pendidikan + perCategory.lainnya
    ];
    window.categoryLabels = catLabels;
    window.categoryAmounts = catAmts;
    window.categoryColors = ['#3b82f6', '#ef4444', '#22c55e', '#f59e0b', '#8b5cf6', '#6b7280'];
}

/* ---- Sidebar Toggle ---- */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }

    // Restore sidebar state
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }
}

/* ---- Theme Toggle ---- */
function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;
    const icon = themeToggle?.querySelector('i');

    // Restore theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon(icon, savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const current = html.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon(icon, next);

            // Recreate charts with new theme colors
            initCharts();
        });
    }
}

function updateThemeIcon(icon, theme) {
    if (!icon) return;
    icon.className = theme === 'dark' ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
}

/* ---- Charts ---- */
function initCharts() {
    renderIncomeExpenseChart();
    renderCategoryChart();
}

function getChartColors() {
    const isDark = document.documentElement.getAttribute('data-theme') !== 'light';
    return {
        gridColor: isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)',
        textColor: isDark ? '#8b92a8' : '#64748b',
        incomeColor: '#10b981',
        expenseColor: '#f43f5e',
        incomeBg: 'rgba(16, 185, 129, 0.12)',
        expenseBg: 'rgba(244, 63, 94, 0.12)',
    };
}

function renderIncomeExpenseChart() {
    const canvas = document.getElementById('incomeExpenseChart');
    if (!canvas) return;

    // Destroy previous chart instance
    const existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();

    const colors = getChartColors();

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: typeof chartLabels !== 'undefined' ? chartLabels : [],
            datasets: [
                {
                    label: 'Pemasukan',
                    data: typeof incomeData !== 'undefined' ? incomeData : [],
                    backgroundColor: colors.incomeColor,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Pengeluaran',
                    data: typeof expenseData !== 'undefined' ? expenseData : [],
                    backgroundColor: colors.expenseColor,
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        color: colors.textColor,
                        font: { family: 'Inter', size: 12 },
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        padding: 20,
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 34, 53, 0.95)',
                    titleColor: '#f1f3f9',
                    bodyColor: '#8b92a8',
                    titleFont: { family: 'Inter', weight: '600' },
                    bodyFont: { family: 'Inter' },
                    padding: 14,
                    cornerRadius: 10,
                    displayColors: true,
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        color: colors.textColor,
                        font: { family: 'Inter', size: 12 },
                    },
                    border: { display: false },
                },
                y: {
                    grid: { color: colors.gridColor },
                    ticks: {
                        color: colors.textColor,
                        font: { family: 'Inter', size: 11 },
                        callback: function (value) {
                            return 'Rp ' + (value / 1000000).toFixed(0) + 'jt';
                        },
                    },
                    border: { display: false },
                },
            },
        },
    });
}

function renderCategoryChart() {
    const canvas = document.getElementById('categoryChart');
    if (!canvas) return;

    const existingChart = Chart.getChart(canvas);
    if (existingChart) existingChart.destroy();

    const colors = getChartColors();

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: typeof categoryLabels !== 'undefined' ? categoryLabels : [],
            datasets: [
                {
                    data: typeof categoryAmounts !== 'undefined' ? categoryAmounts : [],
                    backgroundColor: typeof categoryColors !== 'undefined' ? categoryColors : [],
                    borderWidth: 0,
                    hoverOffset: 8,
                    spacing: 3,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(30, 34, 53, 0.95)',
                    titleColor: '#f1f3f9',
                    bodyColor: '#8b92a8',
                    titleFont: { family: 'Inter', weight: '600' },
                    bodyFont: { family: 'Inter' },
                    padding: 14,
                    cornerRadius: 10,
                    callbacks: {
                        label: function (ctx) {
                            return ctx.label + ': Rp ' + ctx.parsed.toLocaleString('id-ID');
                        },
                    },
                },
            },
        },
    });
}

/* ---- Scroll Animations ---- */
function initAnimations() {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.1 }
    );

    document.querySelectorAll('.card').forEach((card) => {
        observer.observe(card);
    });

    // Animate transaction rows on load
    document.querySelectorAll('.transaction-row').forEach((row, i) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-10px)';
        row.style.transition = `all 0.3s ease-out ${i * 0.05}s`;
        setTimeout(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 100);
    });
}
