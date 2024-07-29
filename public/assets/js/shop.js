document.addEventListener('DOMContentLoaded', () => {
    // Gestion des quantités
    const quantityInput = document.getElementById('quantityInput');
    const incrementBtn = document.getElementById('incrementBtn');
    const decrementBtn = document.getElementById('decrementBtn');

    if (quantityInput && incrementBtn && decrementBtn) {
        incrementBtn.addEventListener('click', () => {
            let currentQuantity = parseInt(quantityInput.value, 10);
            if (currentQuantity < parseInt(quantityInput.max, 10)) {
                quantityInput.value = currentQuantity + 1;
            }
        });

        decrementBtn.addEventListener('click', () => {
            let currentQuantity = parseInt(quantityInput.value, 10);
            if (currentQuantity > 1) {
                quantityInput.value = currentQuantity - 1;
            }
        });

        quantityInput.addEventListener('change', () => {
            let currentQuantity = parseInt(quantityInput.value, 10);
            if (currentQuantity < 1) {
                quantityInput.value = 1;
            } else if (currentQuantity > parseInt(quantityInput.max, 10)) {
                quantityInput.value = parseInt(quantityInput.max, 10);
            }
        });
    }

    // Gestion de la notation
    const ratingInput = document.querySelector('#ratingInput');
    const starRating = document.querySelector('.star-rating');

    if (starRating && ratingInput) {
        starRating.addEventListener('click', function (event) {
            if (event.target.matches('i')) {
                const ratingValue = event.target.getAttribute('data-rating');
                ratingInput.value = ratingValue;

                // Mise à jour des étoiles affichées
                starRating.querySelectorAll('i').forEach(function (star) {
                    const starRatingValue = star.getAttribute('data-rating');
                    if (starRatingValue <= ratingValue) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            }
        });

        // Met à jour l'affichage des étoiles lors du survol
        starRating.addEventListener('mouseover', function (event) {
            if (event.target.matches('i')) {
                const ratingValue = event.target.getAttribute('data-rating');
                starRating.querySelectorAll('i').forEach(function (star) {
                    const starRatingValue = star.getAttribute('data-rating');
                    if (starRatingValue <= ratingValue) {
                        star.classList.add('fas');
                        star.classList.remove('far');
                    } else {
                        star.classList.add('far');
                        star.classList.remove('fas');
                    }
                });
            }
        });

        // Réinitialise les étoiles lors du survol
        starRating.addEventListener('mouseout', function () {
            const currentRating = ratingInput.value;
            starRating.querySelectorAll('i').forEach(function (star) {
                const starRatingValue = star.getAttribute('data-rating');
                if (starRatingValue <= currentRating) {
                    star.classList.add('fas');
                    star.classList.remove('far');
                } else {
                    star.classList.add('far');
                    star.classList.remove('fas');
                }
            });
        });
    }
});
