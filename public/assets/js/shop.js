const incrementBtn = document.getElementById('incrementBtn');
const decrementBtn = document.getElementById('decrementBtn');
const quantityInput = document.getElementById('quantityInput');
const addToCartBtn = document.getElementById('addToCartBtn');

incrementBtn.addEventListener('click', () => {
quantityInput.stepUp();
});

decrementBtn.addEventListener('click', () => {
quantityInput.stepDown();
});

quantityInput.addEventListener('change', () => {
if (quantityInput.value < 1) {
quantityInput.value = 1;
} else if (quantityInput.value > {{ product.stock }}) {
quantityInput.value = {{ product.stock }};
}
});

addToCartBtn.href = addToCartBtn.href.replace('QUANTITY', quantityInput.value);