
document.addEventListener('DOMContentLoaded', () => {
const quantityInput = document.getElementById('quantityInput');
const incrementBtn = document.getElementById('incrementBtn');
const decrementBtn = document.getElementById('decrementBtn');

incrementBtn.addEventListener('click', () => {
let currentQuantity = parseInt(quantityInput.value);
if (currentQuantity < parseInt(quantityInput.max)) {
quantityInput.value = currentQuantity + 1;
}
});

decrementBtn.addEventListener('click', () => {
let currentQuantity = parseInt(quantityInput.value);
if (currentQuantity > 1) {
quantityInput.value = currentQuantity - 1;
}
});

quantityInput.addEventListener('change', () => {
let currentQuantity = parseInt(quantityInput.value);
if (currentQuantity < 1) {
quantityInput.value = 1;
} else if (currentQuantity > parseInt(quantityInput.max)) {
quantityInput.value = parseInt(quantityInput.max);
}
});
});
