document.addEventListener('DOMContentLoaded', function () {
    // フォームのバリデーションを無効化
    const form = document.querySelector('form');
    if (form) {
        form.setAttribute('novalidate', true);
    }

    const paymentSelect = document.getElementById('payment');
    const selectedPaymentDisplay = document.getElementById('selected-payment');

    if (paymentSelect && selectedPaymentDisplay) {
        function updatePaymentDisplay() {
            const value = paymentSelect.value;
            selectedPaymentDisplay.textContent = value === 'credit' ? 'カード支払い' :
                                                  value === 'convenience' ? 'コンビニ支払い' : '未選択';
        }

        paymentSelect.addEventListener('change', updatePaymentDisplay);
        updatePaymentDisplay();
    }
});