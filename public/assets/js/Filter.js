import { Flipper, spring } from 'flip-toolkit';
console.log('Script loaded');

export default class Filter {
    constructor(element) {
        if (element === null) {
            return;
        }
        console.log('Initializing Filter');
        this.pagination = element.querySelector('.js-filter-pagination');
        this.content = element.querySelector('.js-filter-content');
        this.sorting = element.querySelector('.js-filter-sorting');
        this.form = element.querySelector('.js-filter-form');
        this.reset = element.querySelector('#resetBtn');
        this.bindEvents();
    }

    bindEvents() {
        const aClickListener = e => {
            if (e.target.tagName === 'A') {
                e.preventDefault();
                this.loadUrl(e.target.getAttribute('href'));
            }
        };
        this.sorting.addEventListener('click', aClickListener);
        this.pagination.addEventListener('click', aClickListener);
        this.form.querySelectorAll('input').forEach(input => {
            input.addEventListener('change', this.loadForm.bind(this));
        });
        this.form.querySelector('#q').addEventListener('keyup', this.loadForm.bind(this));
        this.reset.addEventListener('click', this.resetForm.bind(this));
    }

    async loadForm() {
        console.log('Loading form');
        const data = new FormData(this.form);
        const url = new URL(this.form.getAttribute('action') || window.location.href);
        const params = new URLSearchParams();
        data.forEach((value, key) => {
            params.append(key, value);
        });
        return this.loadUrl(url.pathname + '?' + params.toString());
    }

    async loadUrl(url) {
        console.log('Loading URL:', url);
        this.showLoader();
        const params = new URLSearchParams(url.split('?')[1] || '');
        params.set('ajax', 1);
        const response = await fetch(url.split('?')[0] + '?' + params.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (response.status >= 200 && response.status < 300) {
            const data = await response.json();
            this.flipContent(data.content);
            this.sorting.innerHTML = data.sorting;
            this.pagination.innerHTML = data.pagination;
            document.getElementById('totalItems').innerText = data.totalItems + ' résultat(s)';
            params.delete('ajax');
            history.replaceState({}, '', url.split('?')[0] + '?' + params.toString());
        } else {
            console.error(response);
        }
        this.hideLoader();
    }

    resetForm() {
        console.log('Resetting form');
        this.form.reset();
        this.loadForm();
    }

    showLoader() {
        this.form.classList.add('is-loading');
        const loader = this.form.querySelector('.js-loading');
        if (loader !== null) {
            loader.setAttribute('aria-hidden', 'false');
            loader.style.display = null;
        }
    }

    hideLoader() {
        this.form.classList.remove('is-loading');
        const loader = this.form.querySelector('.js-loading');
        if (loader !== null) {
            loader.setAttribute('aria-hidden', 'true');
            loader.style.display = 'none';
        }
    }

    flipContent(content) {
        const springConfig = 'gentle';
        const exitSpring = function (element, index, onComplete) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [0, -20],
                    opacity: [1, 0]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                onComplete
            });
        };
        const appearSpring = function (element, index) {
            spring({
                config: 'stiff',
                values: {
                    translateY: [20, 0],
                    opacity: [0, 1]
                },
                onUpdate: ({ translateY, opacity }) => {
                    element.style.opacity = opacity;
                    element.style.transform = `translateY(${translateY}px)`;
                },
                delay: index * 10
            });
        };
        const flipper = new Flipper({ element: this.content });
        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                shouldFlip: false,
                onExit: exitSpring
            });
        });
        flipper.recordBeforeUpdate();
        this.content.innerHTML = content;
        Array.from(this.content.children).forEach(element => {
            flipper.addFlipped({
                element,
                spring: springConfig,
                flipId: element.id,
                onAppear: appearSpring
            });
        });
        flipper.update();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const filterElement = document.querySelector('.js-filter-form');
    if (filterElement) {
        new Filter(filterElement);
    }
});
