/**
 * Tools Section — Sidebar filter, scroll spy, hero search integration
 */
(function () {
    'use strict';

    function initToolsSection() {
        var main = document.querySelector('.tctp-main');
        if (!main) return;

        var links    = Array.from(main.querySelectorAll('.tctp-rail-link'));
        var sections = Array.from(main.querySelectorAll('.tctp-section'));
        var activeCategory = 'all';

        /* ---------------------------------------------------------------
           Filter by category
        --------------------------------------------------------------- */
        function filterByCategory(cat) {
            activeCategory = cat;

            links.forEach(function (link) {
                link.classList.toggle('on', link.getAttribute('data-category') === cat);
            });

            if (cat === 'all') {
                sections.forEach(function (section) {
                    section.setAttribute('data-hidden', 'false');
                });
            } else {
                sections.forEach(function (section) {
                    var sectionCat = section.id.replace('tctp-', '');
                    section.setAttribute('data-hidden', sectionCat === cat ? 'false' : 'true');
                });
            }
        }

        links.forEach(function (link) {
            link.addEventListener('click', function () {
                var cat = this.getAttribute('data-category');
                filterByCategory(cat);

                /* Scroll to top of sections when clicking a category */
                if (cat !== 'all') {
                    var target = document.getElementById('tctp-' + cat);
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                } else {
                    main.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        /* ---------------------------------------------------------------
           Scroll spy — activate sidebar link based on scroll position
        --------------------------------------------------------------- */
        var scrollSpyEnabled = true;

        function onScroll() {
            if (!scrollSpyEnabled) return;
            if (activeCategory !== 'all') return;

            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            var offset = 200;
            var current = null;

            sections.forEach(function (section) {
                if (section.getAttribute('data-hidden') === 'true') return;
                var top = section.getBoundingClientRect().top + scrollTop - offset;
                if (scrollTop >= top) {
                    current = section.id.replace('tctp-', '');
                }
            });

            if (current) {
                links.forEach(function (link) {
                    var cat = link.getAttribute('data-category');
                    if (cat !== 'all') {
                        link.classList.toggle('on', cat === current);
                    } else {
                        link.classList.remove('on');
                    }
                });
            } else {
                /* At top — highlight All */
                links.forEach(function (link) {
                    var cat = link.getAttribute('data-category');
                    link.classList.toggle('on', cat === 'all');
                });
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });

        /* ---------------------------------------------------------------
           Hero search integration
        --------------------------------------------------------------- */
        var heroInput = document.getElementById('tctp-search-input');
        if (heroInput) {
            heroInput.addEventListener('input', function () {
                var query = this.value.toLowerCase().trim();
                var cards = Array.from(main.querySelectorAll('.tctp-card'));

                cards.forEach(function (card) {
                    var text = card.textContent.toLowerCase();
                    var hidden = query && !text.includes(query);
                    card.setAttribute('data-hidden', hidden ? 'true' : 'false');
                });

                sections.forEach(function (section) {
                    var visibleCards = section.querySelectorAll('.tctp-card[data-hidden="false"]');
                    section.setAttribute('data-hidden', visibleCards.length === 0 ? 'true' : 'false');
                });

                /* Reset sidebar to All when searching */
                if (query) {
                    scrollSpyEnabled = false;
                    links.forEach(function (link) {
                        link.classList.toggle('on', link.getAttribute('data-category') === 'all');
                    });
                } else {
                    scrollSpyEnabled = true;
                    onScroll();
                }
            });
        }

        /* ---------------------------------------------------------------
           Sticky sidebar — unstick at section boundary
           Uses IntersectionObserver on .tctp-main so the rail stops
           sticking once the user scrolls past the tools section.
        --------------------------------------------------------------- */
        var rail = main.querySelector('.tctp-rail');
        if (rail && 'IntersectionObserver' in window) {
            var boundaryObserver = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        rail.classList.remove('tctp-rail-unstuck');
                    } else {
                        var rect = entry.boundingClientRect;
                        if (rect.bottom < 0) {
                            rail.classList.add('tctp-rail-unstuck');
                        }
                    }
                });
            }, { threshold: 0, rootMargin: '-92px 0px 0px 0px' });
            boundaryObserver.observe(main);
        }

        /* ---------------------------------------------------------------
           Initial state — default to All
        --------------------------------------------------------------- */
        filterByCategory('all');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initToolsSection);
    } else {
        initToolsSection();
    }

    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('elementor/frontend/init', function () {
            initToolsSection();
        });
        jQuery(document).on('ajaxComplete', function () {
            setTimeout(initToolsSection, 100);
        });
    }
})();
