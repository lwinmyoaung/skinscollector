    // Quantity Logic
    const qtyInput = document.getElementById('qtyInput');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');
    const footerTotal = document.getElementById('footerTotal');

    function updateFooterTotal() {
        const selectedRadio = document.querySelector('input[name="product_id"]:checked');
        
        if (selectedRadio) {
            const rawPrice = parseFloat(selectedRadio.dataset.rawPrice);
            const quantity = parseInt(qtyInput.value) || 1;
            const total = rawPrice * quantity;
            
            if (footerTotal) {
                footerTotal.textContent = new Intl.NumberFormat().format(total) + ' Ks';
            }
        } else {
            if (footerTotal) {
                footerTotal.textContent = '0 Ks';
            }
        }
    }

    if (qtyMinus) {
        qtyMinus.addEventListener('click', function() {
            let val = parseInt(qtyInput.value) || 1;
            if (val > 1) {
                qtyInput.value = val - 1;
                updateFooterTotal();
            }
        });
    }

    if (qtyPlus) {
        qtyPlus.addEventListener('click', function() {
            let val = parseInt(qtyInput.value) || 1;
            qtyInput.value = val + 1;
            updateFooterTotal();
        });
    }

    const productRadios = document.querySelectorAll('input[name="product_id"]');
    productRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateFooterTotal();
        });
    });

    // Initialize total
    updateFooterTotal();