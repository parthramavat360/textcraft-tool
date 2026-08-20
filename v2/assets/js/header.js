/**
 * TextCraft Tools Pro — Header Script
 *
 * Handles mega menu toggle, mobile hamburger, and keyboard accessibility.
 * Init runs inside DOMContentLoaded; observer re-binds after Elementor ajax loads.
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------------
       Mega Menu — Toggle open/close on desktop (hover) & mobile (tap)
       ------------------------------------------------------------------ */
    function initMegaMenus() {
        var wraps = document.querySelectorAll('.tctp-mega-wrap');
        if (!wraps.length) return;

        wraps.forEach(function (wrap) {
            var btn    = wrap.querySelector('.tctp-mega-trigger');
            var panel  = wrap.querySelector('.tctp-mega');
            var timer  = null;

            if (!btn || !panel) return;

            /** Set open state and update aria attribute */
            function setOpen(on) {
                wrap.classList.toggle('open', on);
                btn.setAttribute('aria-expanded', String(on));
            }

            /* Desktop: click toggles */
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                clearTimeout(timer);
                setOpen(!wrap.classList.contains('open'));
            });

            /* Desktop: hover keeps menu open */
            wrap.addEventListener('mouseenter', function () {
                clearTimeout(timer);
                setOpen(true);
            });

            wrap.addEventListener('mouseleave', function () {
                timer = setTimeout(function () { setOpen(false); }, 160);
            });

            /* Close when any link inside mega is clicked */
            panel.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () { setOpen(false); });
            });

            /* Close on outside click */
            document.addEventListener('click', function (e) {
                if (!wrap.contains(e.target)) setOpen(false);
            });

            /* Close on Escape */
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setOpen(false);
            });
        });
    }

    /* ------------------------------------------------------------------
       Mobile Hamburger — Toggle slide-out nav on ≤640px screens
       ------------------------------------------------------------------ */
    function initHamburger() {
        var hamburger = document.querySelector('.tctp-hamburger');
        var navInner  = document.querySelector('.tctp-nav-inner');
        if (!hamburger || !navInner) return;

        hamburger.addEventListener('click', function () {
            var isOpen = navInner.classList.toggle('is-open');
            hamburger.classList.toggle('is-active', isOpen);
            hamburger.setAttribute('aria-expanded', String(isOpen));
        });

        /* Close menu when a nav link is tapped (mobile) */
        navInner.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () {
                navInner.classList.remove('is-open');
                hamburger.classList.remove('is-active');
                hamburger.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* ------------------------------------------------------------------
       Initialize on DOM ready
       ------------------------------------------------------------------ */
    function init() {
        initMegaMenus();
        initHamburger();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    /* ------------------------------------------------------------------
       Re-init after Elementor AJAX page loads (e.g. soft navigation)
       ------------------------------------------------------------------ */
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('ajax_complete', function () {
            initMegaMenus();
            initHamburger();
        });
    }
})();
