/**
 * Real-time Dashboard Updates via AJAX Polling
 * Refreshes statistics every 30 seconds
 */
(function () {
    const POLL_INTERVAL = 30000; // 30 seconds

    const els = {
        totalProducts: document.getElementById('stat-total-products'),
        lowStock: document.getElementById('stat-low-stock'),
        outOfStock: document.getElementById('stat-out-of-stock'),
        totalValue: document.getElementById('stat-total-value'),
        lastUpdated: document.getElementById('last-updated'),
        lowStockList: document.getElementById('low-stock-list'),
        recentTransactions: document.getElementById('recent-transactions'),
    };

    async function fetchStats() {
        try {
            const res = await fetch('/api/dashboard/stats', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();

            if (els.totalProducts) animateValue(els.totalProducts, data.total_products);
            if (els.lowStock) animateValue(els.lowStock, data.low_stock_count);
            if (els.outOfStock) animateValue(els.outOfStock, data.out_of_stock_count);
            if (els.totalValue) {
                els.totalValue.textContent = '₱' + Number(data.total_value).toLocaleString('en-PH', {
                    minimumFractionDigits: 2, maximumFractionDigits: 2
                });
            }
            if (els.lastUpdated) {
                els.lastUpdated.textContent = 'Last updated: ' + new Date(data.updated_at).toLocaleTimeString();
            }
        } catch (e) {
            console.warn('Failed to fetch dashboard stats', e);
        }
    }

    async function fetchLowStock() {
        if (!els.lowStockList) return;
        try {
            const res = await fetch('/api/products/low-stock', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const products = await res.json();

            if (products.length === 0) {
                els.lowStockList.innerHTML = '<div class="p-3 text-center text-muted">All products sufficiently stocked.</div>';
                return;
            }

            els.lowStockList.innerHTML = products.map(p => `
                <a href="/products/${p.id}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <strong>${p.name}</strong><br>
                        <small class="text-muted">${p.sku} · ${p.category}</small>
                    </div>
                    <span class="badge ${p.quantity <= 0 ? 'bg-danger' : 'bg-warning text-dark'}">
                        Qty: ${p.quantity}
                    </span>
                </a>
            `).join('');
        } catch (e) {
            console.warn('Failed to fetch low stock', e);
        }
    }

    async function fetchRecentTransactions() {
        if (!els.recentTransactions) return;
        try {
            const res = await fetch('/api/transactions/recent', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const items = await res.json();

            if (items.length === 0) {
                els.recentTransactions.innerHTML = '<div class="p-3 text-center text-muted">No recent transactions.</div>';
                return;
            }

            els.recentTransactions.innerHTML = items.map(t => `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <strong>${t.product_name || '—'}</strong>
                        <span class="badge ${t.signed_quantity > 0 ? 'bg-success' : 'bg-danger'}">
                            ${t.signed_quantity > 0 ? '+' : ''}${t.signed_quantity}
                        </span>
                    </div>
                    <small class="text-muted">${t.type_label} · ${t.user_name} · ${t.created_at_human}</small>
                </div>
            `).join('');
        } catch (e) {
            console.warn('Failed to fetch recent transactions', e);
        }
    }

    function animateValue(el, newVal) {
        const current = parseInt(el.textContent.replace(/[^0-9-]/g, '')) || 0;
        if (current === newVal) return;
        el.textContent = newVal;
        el.classList.add('text-primary');
        setTimeout(() => el.classList.remove('text-primary'), 600);
    }

    function poll() {
        fetchStats();
        fetchLowStock();
        fetchRecentTransactions();
    }

    // Initial load + interval
    poll();
    setInterval(poll, POLL_INTERVAL);
})();
