(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function reveal() {
        document.body.classList.add('is-loaded');
    }

    if (prefersReducedMotion || document.readyState === 'complete') {
        reveal();
    } else {
        window.addEventListener('load', reveal);
    }
})();