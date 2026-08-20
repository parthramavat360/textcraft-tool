/**
 * TextCraft Tools — Dark Mode Toggle
 *
 * Toggles dark mode via [data-theme="dark"] on <html>.
 * Persists preference in localStorage.
 * Injects a toggle button into the site header.
 *
 * @package TextCraft_Tools
 */
(function () {
    'use strict';

    var KEY = 'tc-theme';
    var html = document.documentElement;

    // Apply stored preference immediately (before paint)
    var stored = null;
    try { stored = localStorage.getItem(KEY); } catch (e) {}

    if (stored === 'dark') {
        html.setAttribute('data-theme', 'dark');
    } else if (stored === 'light') {
        html.removeAttribute('data-theme');
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        html.setAttribute('data-theme', 'dark');
    }

    function isDark() {
        return html.getAttribute('data-theme') === 'dark';
    }

    // Update toggle button icons
    function updateIcons() {
        var btns = document.querySelectorAll('.tc-theme-toggle');
        for (var i = 0; i < btns.length; i++) {
            btns[i].textContent = isDark() ? '\u2600\uFE0F' : '\uD83C\uDF19';
            btns[i].setAttribute('aria-label', isDark() ? 'Switch to light mode' : 'Switch to dark mode');
        }
    }

    // Inject toggle button into the site header
    function injectToggle() {
        if (document.querySelector('.tc-theme-toggle')) return;

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'tc-theme-toggle';
        btn.setAttribute('aria-label', isDark() ? 'Switch to light mode' : 'Switch to dark mode');
        btn.textContent = isDark() ? '\u2600\uFE0F' : '\uD83C\uDF19';

        // Try to insert next to hamburger (mobile) or in header nav (desktop)
        var hamburger = document.querySelector('.hamburger');
        var siteNav = document.querySelector('.site-nav');
        var headerInner = document.querySelector('.header-inner');

        if (hamburger && hamburger.parentNode) {
            hamburger.parentNode.insertBefore(btn, hamburger);
        } else if (siteNav && siteNav.parentNode) {
            siteNav.parentNode.insertBefore(btn, siteNav);
        } else if (headerInner) {
            headerInner.appendChild(btn);
        }
    }

    // Toggle on click (delegated)
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.tc-theme-toggle');
        if (!btn) return;

        if (isDark()) {
            html.removeAttribute('data-theme');
            try { localStorage.setItem(KEY, 'light'); } catch (e) {}
        } else {
            html.setAttribute('data-theme', 'dark');
            try { localStorage.setItem(KEY, 'dark'); } catch (e) {}
        }
        updateIcons();
    });

    // Sync if OS preference changes
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if (localStorage.getItem(KEY)) return;
            if (e.matches) {
                html.setAttribute('data-theme', 'dark');
            } else {
                html.removeAttribute('data-theme');
            }
            updateIcons();
        });
    }

    // Init on DOM ready
    function init() {
        injectToggle();
        updateIcons();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
