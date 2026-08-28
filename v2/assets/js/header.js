/**
 * TextCraft Tools Pro — Header Script
 *
 * Handles mega menu toggle, mobile slide-out hamburger menu, overlay backdrop,
 * body-scroll lock, safe-area aware sizing, and keyboard accessibility.
 * Init runs inside DOMContentLoaded; observer re-binds after Elementor ajax loads.
 */
(function () {
    'use strict';

    var overlay = null;

    /* ------------------------------------------------------------------
       Body scroll lock — disable page scroll while the mobile panel is open
       ------------------------------------------------------------------ */
    function setScrollLock(on) {
        var html = document.documentElement;
        if (on) {
            var top = window.pageYOffset || document.documentElement.scrollTop;
            html.style.setProperty('--tctp-scroll-top', top + 'px');
            html.classList.add('tctp-scroll-locked');
        } else {
            html.classList.remove('tctp-scroll-locked');
        }
    }

    /* ------------------------------------------------------------------
       Mega Menu — desktop dropdown + mobile accordion toggle
       ------------------------------------------------------------------ */
    function initMegaMenus(header) {
        var wraps = header.querySelectorAll('.tctp-mega-wrap');
        if (!wraps.length) return;

        wraps.forEach(function (wrap) {
            if (wrap.__tctpBound) return;
            wrap.__tctpBound = true;

            var btn   = wrap.querySelector('.tctp-mega-trigger');
            var panel = wrap.querySelector('.tctp-mega');
            var timer = null;

            if (!btn || !panel) return;

            function setOpen(on) {
                wrap.classList.toggle('open', on);
                btn.setAttribute('aria-expanded', String(on));
            }

            /* Toggle on click (works for desktop click + mobile tap) */
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                clearTimeout(timer);
                setOpen(!wrap.classList.contains('open'));
            });

            /* Desktop: keep open on hover, close after a short delay */
            wrap.addEventListener('mouseenter', function () {
                clearTimeout(timer);
                setOpen(true);
            });

            wrap.addEventListener('mouseleave', function () {
                timer = setTimeout(function () { setOpen(false); }, 160);
            });

            /* Close after clicking any grandchild link */
            panel.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () { setOpen(false); });
            });

            /* Close on Escape */
            btn.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setOpen(false);
            });
        });
    }

    /* ------------------------------------------------------------------
       Mobile slide-out menu — hamburger + overlay + scroll lock
       ------------------------------------------------------------------ */
    function initMobileNav(header) {
        var hamburger = header.querySelector('.tctp-hamburger');
        var navInner  = header.querySelector('.tctp-nav-inner');
        if (!hamburger || !navInner) return;

        function closeMenu() {
            navInner.classList.remove('is-open');
            hamburger.classList.remove('is-active');
            hamburger.setAttribute('aria-expanded', 'false');
            if (overlay) overlay.classList.remove('is-open');
            setScrollLock(false);
        }

        function openMenu() {
            /* Ensure the desktop mega overlay isn't left open */
            header.querySelectorAll('.tctp-mega-wrap').forEach(function (w) {
                w.classList.remove('open');
                var b = w.querySelector('.tctp-mega-trigger');
                if (b) b.setAttribute('aria-expanded', 'false');
            });
            navInner.classList.add('is-open');
            hamburger.classList.add('is-active');
            hamburger.setAttribute('aria-expanded', 'true');
            if (overlay) overlay.classList.add('is-open');
            setScrollLock(true);
        }

        if (hamburger.__tctpBound) return;
        hamburger.__tctpBound = true;

        hamburger.addEventListener('click', function () {
            var isOpen = navInner.classList.contains('is-open');
            if (isOpen) { closeMenu(); } else { openMenu(); }
        });

        /* Close when a link inside the panel is tapped */
        navInner.querySelectorAll('a').forEach(function (a) {
            if (a.__tctpLinkBound) return;
            a.__tctpLinkBound = true;
            a.addEventListener('click', closeMenu);
        });

        /* Close on outside click (overlay) */
        if (overlay) {
            overlay.addEventListener('click', closeMenu);
        }

        /* Close on Escape */
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && navInner.classList.contains('is-open')) {
                closeMenu();
            }
        });

        /* Close if the viewport grows back to desktop while the panel is open */
        window.addEventListener('resize', function () {
            if (window.innerWidth > 1024 && navInner.classList.contains('is-open')) {
                closeMenu();
            }
        });
    }

    /* ------------------------------------------------------------------
       Initialize on DOM ready
       ------------------------------------------------------------------ */
    function init() {
        var headers = document.querySelectorAll('.tctp-header');
        headers.forEach(function (header) {
            if (!overlay) {
                overlay = header.querySelector('.tctp-overlay');
            }
            initMegaMenus(header);
            initMobileNav(header);
        });
    }

    /* Scroll-lock helper CSS (added once) */
    function ensureScrollLockStyle() {
        if (document.getElementById('tctp-scroll-lock-style')) return;
        var style = document.createElement('style');
        style.id = 'tctp-scroll-lock-style';
        style.textContent =
            '.tctp-scroll-locked, .tctp-scroll-locked body { overflow: hidden !important; }' +
            '.tctp-scroll-locked { position: fixed; width: 100%; top: var(--tctp-scroll-top, 0); }';
        document.head.appendChild(style);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            ensureScrollLockStyle();
            init();
        });
    } else {
        ensureScrollLockStyle();
        init();
    }

    /* Re-init after Elementor AJAX page loads (e.g. soft navigation) */
    if (typeof jQuery !== 'undefined') {
        jQuery(document).on('ajax_complete', function () {
            ensureScrollLockStyle();
            init();
        });
    }
})();
