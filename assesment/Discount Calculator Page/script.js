const TAX_RATE = 5;

const originalPriceInput = document.getElementById('original-price');
const discountPercentageInput = document.getElementById('discount-percentage');
const finalPriceInput = document.getElementById('final-price');
const taxRateDisplay = document.getElementById('tax-rate');

const priceError = document.getElementById('price-error');
const discountError = document.getElementById('discount-error');

let budgetDealAlertShown = false;

taxRateDisplay.textContent = TAX_RATE;

function updateFinalPrice() {
    let originalPrice = Number(originalPriceInput.value);
    let discountPercentage = Number(discountPercentageInput.value);

    if (Number.isNaN(originalPrice)) {
        originalPrice = 0;
    }

    if (Number.isNaN(discountPercentage)) {
        discountPercentage = 0;
    }

    priceError.textContent = '';
    discountError.textContent = '';

    if (originalPrice < 0) {
        originalPrice = 0;
        originalPriceInput.value = 0;
        priceError.textContent = 'Original price cannot be less than 0.';
    }

    if (discountPercentage < 0) {
        discountPercentage = 0;
        discountPercentageInput.value = 0;
        discountError.textContent = 'Discount percentage cannot be less than 0.';
    }

    if (discountPercentage > 100) {
        discountPercentage = 100;
        discountPercentageInput.value = 100;
        discountError.textContent = 'Discount percentage cannot be more than 100.';
    }

    const discountAmount = (originalPrice * discountPercentage) / 100;
    const discountedPrice = originalPrice - discountAmount;
    const taxAmount = (discountedPrice * TAX_RATE) / 100;
    const finalPrice = discountedPrice + taxAmount;

    finalPriceInput.value = `৳${finalPrice.toFixed(2)}`;

    if (finalPrice < 500 && finalPrice > 0 && !budgetDealAlertShown) {
        alert('Congratulations! You unlocked a Budget Deal.');
        budgetDealAlertShown = true;
    }

    if (finalPrice >= 500 || finalPrice === 0) {
        budgetDealAlertShown = false;
    }
}

originalPriceInput.addEventListener('input', updateFinalPrice);
discountPercentageInput.addEventListener('input', updateFinalPrice);

updateFinalPrice();