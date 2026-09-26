// StockMaster Global Interactivity

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }
}

// Quick Stock Adjustment Modal
function openQuickStockModal(productId = '', productName = '', currentStock = 0, defaultType = 'IN') {
    const modal = document.getElementById('stockMovementModal');
    if (!modal) return;

    const productSelect = document.getElementById('modal_product_id');
    const typeSelect = document.getElementById('modal_type');
    const currentStockInfo = document.getElementById('modal_current_stock_display');

    if (productSelect && productId) {
        productSelect.value = productId;
    }
    if (typeSelect) {
        typeSelect.value = defaultType;
        handleTypeChange(defaultType);
    }
    if (currentStockInfo) {
        currentStockInfo.textContent = `Current Stock: ${currentStock} units`;
    }

    openModal('stockMovementModal');
}

function handleTypeChange(type) {
    const qtyLabel = document.getElementById('modal_qty_label');
    const qtyHelp = document.getElementById('modal_qty_help');
    
    if (type === 'IN') {
        if (qtyLabel) qtyLabel.textContent = 'Quantity to Receive (+)';
        if (qtyHelp) qtyHelp.textContent = 'Adds to current stock.';
    } else if (type === 'OUT') {
        if (qtyLabel) qtyLabel.textContent = 'Quantity to Dispatch (-)';
        if (qtyHelp) qtyHelp.textContent = 'Deducts from current stock.';
    } else if (type === 'ADJUSTMENT') {
        if (qtyLabel) qtyLabel.textContent = 'New Physical Stock Count (=)';
        if (qtyHelp) qtyHelp.textContent = 'Replaces current stock with this counted total.';
    }
}

// Auto close toasts after 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    const toasts = document.querySelectorAll('.alert-toast');
    toasts.forEach(toast => {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    });

    // Close modal on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeModal(backdrop.id);
            }
        });
    });
});
