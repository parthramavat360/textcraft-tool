/**
 * ConvertCase — Header / Mega Menu Controller
 *
 * FIX v2.0.0:
 *  - Removed the mouseleave-closes-mega behaviour which caused the menu
 *    to close before the user's cursor reached it.
 *  - Menu now opens on mouseenter of .nav-item--mega (button OR panel area)
 *    and closes only when the cursor leaves the WHOLE trigger+panel region.
 *  - CSS :hover rule on .nav-item--mega handles the visual open state
 *    independently; JS only manages aria-expanded and the .is-open class
 *    for JS-driven paths (click, keyboard, click-outside).
 *
 * @package  ConvertCase
 * @version  2.0.0
 */

(() => {
    'use strict';

    /* ─────────────────────────────────────────────
       DOM REFERENCES
    ───────────────────────────────────────────── */
    const $ = id => document.getElementById(id);
    const DOM = {
        header:      () => $('site-header'),
        megaTrigger: () => $('mega-trigger'),
        megaBtn:     () => $('mega-btn'),
        megaPanel:   () => $('mega-panel'),
        hamburger:   () => $('hamburger'),
        drawer:      () => $('mobile-drawer'),
        overlay:     () => $('mobile-overlay'),
        drawerClose: () => $('drawer-close'),
        accordions:  () => document.querySelectorAll('.mobile-accordion'),
    };

    /* ─────────────────────────────────────────────
       STATE
    ───────────────────────────────────────────── */
    const state = { megaOpen: false, drawerOpen: false };

    /* ─────────────────────────────────────────────
       STICKY HEADER SHADOW
    ───────────────────────────────────────────── */
    const initScrollWatcher = () => {
        const header = DOM.header();
        if (!header) return;

        const sentinel = document.createElement('div');
        sentinel.setAttribute('aria-hidden', 'true');
        sentinel.style.cssText = 'position:absolute;top:0;height:1px;width:1px;pointer-events:none';
        document.body.prepend(sentinel);

        new IntersectionObserver(
            ([entry]) => header.classList.toggle('scrolled', !entry.isIntersecting),
            { rootMargin: '-1px 0px 0px 0px' }
        ).observe(sentinel);
    };

    /* ─────────────────────────────────────────────
       MEGA MENU — desktop
    ───────────────────────────────────────────── */

    /** Sync aria-expanded; panel visibility driven by CSS + .is-open */
    const openMega = () => {
        if (state.megaOpen) return;
        state.megaOpen = true;

        const btn   = DOM.megaBtn();
        const panel = DOM.megaPanel();
        const trigger = DOM.megaTrigger();
        if (!btn || !panel) return;

        // Remove hidden attr so the panel is in the accessibility tree
        panel.removeAttribute('hidden');
        // Force reflow before adding class so CSS transition fires
        panel.getBoundingClientRect();
        panel.classList.add('is-open');
        trigger?.classList.add('mega-is-open');
        btn.setAttribute('aria-expanded', 'true');
    };

    const closeMega = (returnFocus = false) => {
        if (!state.megaOpen) return;
        state.megaOpen = false;

        const btn     = DOM.megaBtn();
        const panel   = DOM.megaPanel();
        const trigger = DOM.megaTrigger();
        if (!btn || !panel) return;

        panel.classList.remove('is-open');
        trigger?.classList.remove('mega-is-open');
        btn.setAttribute('aria-expanded', 'false');

        // Hide from a11y tree after transition
        panel.addEventListener('transitionend', () => {
            if (!state.megaOpen) panel.setAttribute('hidden', '');
        }, { once: true });

        if (returnFocus) btn.focus();
    };

    const toggleMega = () => state.megaOpen ? closeMega(true) : openMega();

    const initMegaMenu = () => {
        const btn     = DOM.megaBtn();
        const panel   = DOM.megaPanel();
        const trigger = DOM.megaTrigger();
        if (!btn || !panel || !trigger) return;

        /* ── Click: toggle ── */
        btn.addEventListener('click', e => {
            e.stopPropagation();
            toggleMega();
        });

        /* ── Hover: open on enter, close when cursor leaves the
              ENTIRE trigger container (button + panel combined).
              Using a small delay prevents accidental closes when
              moving diagonally from button to panel. ── */
        let leaveTimer;

        trigger.addEventListener('mouseenter', () => {
            clearTimeout(leaveTimer);
            openMega();
        });

        trigger.addEventListener('mouseleave', () => {
            // Small delay so cursor can pass through any 1-2px gap
            leaveTimer = setTimeout(() => closeMega(), 80);
        });

        // If mouse re-enters the panel itself, cancel the close
        panel.addEventListener('mouseenter', () => {
            clearTimeout(leaveTimer);
            openMega();
        });

        panel.addEventListener('mouseleave', () => {
            leaveTimer = setTimeout(() => closeMega(), 80);
        });

        /* ── Click outside ── */
        document.addEventListener('click', e => {
            if (state.megaOpen && !trigger.contains(e.target) && !panel.contains(e.target)) {
                closeMega();
            }
        });

        /* ── Keyboard: Tab out of last link closes panel ── */
        panel.addEventListener('keydown', e => {
            if (e.key !== 'Tab') return;
            const focusable = [...panel.querySelectorAll('a, button')];
            const last = focusable[focusable.length - 1];
            if (!e.shiftKey && document.activeElement === last) {
                closeMega(true);
            }
        });
    };

    /* ─────────────────────────────────────────────
       MOBILE DRAWER
    ───────────────────────────────────────────── */
    const setBodyLock = locked => {
        document.body.style.overflow = locked ? 'hidden' : '';
    };

    const openDrawer = () => {
        if (state.drawerOpen) return;
        state.drawerOpen = true;

        const drawer    = DOM.drawer();
        const overlay   = DOM.overlay();
        const hamburger = DOM.hamburger();
        if (!drawer || !overlay || !hamburger) return;

        drawer.removeAttribute('hidden');
        overlay.removeAttribute('hidden');
        drawer.getBoundingClientRect();

        drawer.classList.add('is-open');
        overlay.classList.add('is-open');
        hamburger.setAttribute('aria-expanded', 'true');
        hamburger.setAttribute('aria-label', 'Close navigation menu');

        setBodyLock(true);
        DOM.drawerClose()?.focus();
    };

    const closeDrawer = (returnFocus = false) => {
        if (!state.drawerOpen) return;
        state.drawerOpen = false;

        const drawer    = DOM.drawer();
        const overlay   = DOM.overlay();
        const hamburger = DOM.hamburger();
        if (!drawer || !overlay || !hamburger) return;

        drawer.classList.remove('is-open');
        overlay.classList.remove('is-open');
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.setAttribute('aria-label', 'Open navigation menu');
        setBodyLock(false);

        drawer.addEventListener('transitionend', () => {
            if (!state.drawerOpen) {
                drawer.setAttribute('hidden', '');
                overlay.setAttribute('hidden', '');
            }
        }, { once: true });

        if (returnFocus) hamburger.focus();
    };

    const initDrawer = () => {
        const hamburger   = DOM.hamburger();
        const drawerClose = DOM.drawerClose();
        const overlay     = DOM.overlay();
        if (!hamburger) return;

        hamburger.addEventListener('click',   openDrawer);
        drawerClose?.addEventListener('click', () => closeDrawer(true));
        overlay?.addEventListener('click',    () => closeDrawer(true));
    };

    /* ─────────────────────────────────────────────
       MOBILE ACCORDION
    ───────────────────────────────────────────── */
    const initAccordions = () => {
        DOM.accordions().forEach(accordion => {
            const trigger = accordion.querySelector('.mobile-accordion__trigger');
            const panel   = accordion.querySelector('.mobile-accordion__panel');
            if (!trigger || !panel) return;

            trigger.addEventListener('click', () => {
                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';

                // Collapse all others (single-open)
                DOM.accordions().forEach(other => {
                    if (other === accordion) return;
                    const otherTrigger = other.querySelector('.mobile-accordion__trigger');
                    const otherPanel   = other.querySelector('.mobile-accordion__panel');
                    if (otherTrigger && otherPanel) {
                        otherTrigger.setAttribute('aria-expanded', 'false');
                        otherPanel.setAttribute('hidden', '');
                    }
                });

                trigger.setAttribute('aria-expanded', String(!isExpanded));
                if (isExpanded) {
                    panel.setAttribute('hidden', '');
                } else {
                    panel.removeAttribute('hidden');
                }
            });
        });
    };

    /* ─────────────────────────────────────────────
       GLOBAL KEYBOARD
    ───────────────────────────────────────────── */
    const initKeyboard = () => {
        document.addEventListener('keydown', e => {
            if (e.key !== 'Escape') return;
            if (state.megaOpen)   closeMega(true);
            if (state.drawerOpen) closeDrawer(true);
        });
    };

    /* ─────────────────────────────────────────────
       RESIZE — close drawer when going desktop
    ───────────────────────────────────────────── */
    const initResize = () => {
        let t;
        window.addEventListener('resize', () => {
            clearTimeout(t);
            t = setTimeout(() => {
                if (window.innerWidth > 768 && state.drawerOpen) closeDrawer();
            }, 150);
        });
    };

    /* ─────────────────────────────────────────────
       INIT
    ───────────────────────────────────────────── */
    const init = () => {
        initScrollWatcher();
        initMegaMenu();
        initDrawer();
        initAccordions();
        initKeyboard();
        initResize();
    };

    document.readyState === 'loading'
        ? document.addEventListener('DOMContentLoaded', init)
        : init();

})();
