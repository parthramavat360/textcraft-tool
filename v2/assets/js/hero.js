/**
 * Hero Search — filters all .tctp-card elements on the page.
 */
(function () {
    'use strict';

    function initHeroSearch() {
        var input = document.getElementById('tctp-search-input');
        if (!input) return;

        input.addEventListener('input', function () {
            var query = this.value.toLowerCase().trim();
            var cards = document.querySelectorAll('.tctp-card');
            var sections = document.querySelectorAll('.tctp-section');

            /* Filter cards by text */
            cards.forEach(function (card) {
                var text = card.textContent.toLowerCase();
                var hidden = query && !text.includes(query);
                card.setAttribute('data-hidden', hidden ? 'true' : 'false');
            });

            /* Show/hide sections based on visible cards */
            sections.forEach(function (section) {
                var visibleCards = section.querySelectorAll('.tctp-card[data-hidden="false"]');
                section.setAttribute('data-hidden', visibleCards.length === 0 ? 'true' : 'false');
            });
        });
    }

    function initSearchShortcut() {
        document.addEventListener('keydown', function (e) {
            if (e.key === '/') {
                var active = document.activeElement;
                var tag = active ? active.tagName : '';
                if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || active.isContentEditable) {
                    return;
                }
                e.preventDefault();
                var input = document.getElementById('tctp-search-input');
                if (input) input.focus();
            }
        });
    }

    function init() {
        initHeroSearch();
        initSearchShortcut();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('ajaxComplete', function () {
            initHeroSearch();
        });
    }
})();
