# TextCraft Tools — Premium Redesign Phase

## Design Philosophy

**Not AI-generated. Professional. ConvertCase.net / TinyPNG / ILoveIMG tier.**

- Clean white canvas, tight spacing, restrained shadows
- One accent color (blue) + one warm accent (orange) — used sparingly
- Typography: system font stack, no decorative fonts
- Shadows: barely-there, never colored
- Borders: thin, low-contrast, consistent radius
- Zero animations except micro-interactions (hover lift, accordion expand)
- Cards: flat with subtle border, no gradients, no sheen sweeps

---

## Phase 1: CSS Variable Cleanup (Naming Consistency)

### Problem
`--tc-gold*` variables are actually blue values. `--tc-gold-light` = `#3b82f6` (blue). This is confusing and fragile.

### Action
Rename all `--tc-gold*` tokens to semantic names:

| Old Token | New Token | Value |
|---|---|---|
| `--tc-gold` | `--tc-accent-warm` | `#f97316` |
| `--tc-gold-light` | `--tc-primary` | `#3b82f6` |
| `--tc-gold-dark` | `--tc-primary-dark` | `#1e40af` |
| `--tc-gold-bright` | `--tc-primary-bright` | `#2563eb` |
| `--tc-gold-glow` | `--tc-primary-glow` | `rgba(37,99,235,0.10)` |
| `--tc-shadow-gold` | `--tc-shadow-primary` | `0 4px 12px rgba(37,99,235,0.08)` |
| `--hdr-gold1` | `--hdr-primary` | `#2563eb` |
| `--hdr-gold2` | `--hdr-primary-dark` | `#1d4ed8` |
| `--hdr-gold-light` | `--hdr-primary-light` | `#3b82f6` |

Also add missing tokens:
```css
--tc-radius-xl: 12px;
--tc-shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
--tc-shadow-card: 0 1px 3px rgba(0,0,0,0.06);
--tc-shadow-card-hover: 0 4px 12px rgba(0,0,0,0.08);
--tc-shadow-elevated: 0 8px 24px rgba(0,0,0,0.10);
```

---

## Phase 2: Kill AI-Slop Patterns

### 2A. Orange-Rainbow Button Hover (CRITICAL)
**Current:** `.tc-btn--primary:hover` has `linear-gradient(135deg, #3b82f6, #f97316, #1d4ed8)` — an orange flash.
**Nuclear override** at line ~3948 makes ALL buttons hover orange with `!important`.

**Fix:**
- Primary hover: darken to `--tc-primary-dark` (`#1d4ed8`), no gradient
- Remove the global nuclear override block entirely
- Fix each button variant individually

### 2B. Aggressive Card Hover Shadows
**Current:** `0 24px 70px rgba(37,99,235,0.18)` on 7+ card types. 70px blur is insane.

**Fix:** Replace ALL card hover shadows with `var(--tc-shadow-card-hover)` → `0 4px 12px rgba(0,0,0,0.08)`

### 2C. FAQ Accordion Shadow
**Current:** `box-shadow: 0 16px 45px rgba(0,0,0,0.45)` — 45% opaque black.

**Fix:** `box-shadow: var(--tc-shadow-card)`

### 2D. Card Sheen Sweep
**Current:** Every card has `::before` or `::after` with `linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent)` sweeping across. Nearly invisible on light backgrounds.

**Fix:** Remove all `.tc-card-sheen` and `.tc-card-has-sheen` effects. Cards should be flat.

### 2E. Dust Particles
**Current:** `.tc-section-has-dust` renders 10 animated dots via `::before`.

**Fix:** Already gutted. Just make sure the class does nothing (empty rule).

---

## Phase 3: Unified Card System

### Problem
6 near-identical card patterns copy-pasted:
- `.tc-tool-card`
- `.tc-feature-card`
- `.tc-seo-card`
- `.tc-tool-link-card`
- `.tc-atp-card`
- `.tc-glass-card`

Each has slightly different radius, padding, gradient, shadow.

### Solution — Single Card Pattern
```css
.tc-card {
    background: var(--tc-bg-card);
    border: 1px solid var(--tc-border);
    border-radius: var(--tc-radius-lg);
    padding: 24px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}
.tc-card:hover {
    border-color: var(--tc-primary);
    box-shadow: var(--tc-shadow-card-hover);
    transform: translateY(-2px);
}
```

Then alias old classes to the new one:
```css
.tc-tool-card,
.tc-feature-card,
.tc-seo-card,
.tc-tool-link-card,
.tc-atp-card,
.tc-glass-card { @extend .tc-card; } /* or duplicate the properties */
```

Remove `overflow: hidden` from cards (was only needed for sheen pseudo-elements).

---

## Phase 4: Typography Refinement

### Current State
System font stack everywhere. `--tc-font-display` and `--tc-font-body` are identical.

### Plan
- Keep system fonts (fast, no FOIT/FOUT)
- Increase heading weight to `700` (currently `600` in some places)
- Add `letter-spacing: -0.02em` to h1/h2 for tightness
- Ensure consistent line-heights: headings `1.2`, body `1.6`

---

## Phase 5: Button System Cleanup

### Current Problem
4 variants + nuclear override = fragile specificity war.

### New Button System
```css
.tc-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    border-radius: var(--tc-radius-sm);  /* 4px */
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background 0.15s, color 0.15s, box-shadow 0.15s, transform 0.15s;
}
.tc-btn:active { transform: scale(0.98); }

.tc-btn--primary {
    background: var(--tc-primary);
    color: #ffffff;
}
.tc-btn--primary:hover {
    background: var(--tc-primary-dark);
    box-shadow: 0 2px 8px rgba(37,99,235,0.25);
}

.tc-btn--secondary {
    background: var(--tc-surface-2);
    color: var(--tc-text-primary);
    border: 1px solid var(--tc-border);
}
.tc-btn--secondary:hover {
    background: var(--tc-surface-3);
    border-color: var(--tc-border-hover);
}

.tc-btn--ghost {
    background: transparent;
    color: var(--tc-primary);
    border: 1px solid var(--tc-border);
}
.tc-btn--ghost:hover {
    background: var(--tc-primary-glow);
    border-color: var(--tc-primary);
}

.tc-btn--danger {
    background: var(--tc-danger-soft);
    color: var(--tc-danger);
}
.tc-btn--danger:hover {
    background: var(--tc-danger);
    color: #ffffff;
}
```

Remove: `::after` sheen pseudo-element on buttons. Remove nuclear override block.

---

## Phase 6: Shadow System

### New Shadow Tokens
```css
--tc-shadow-sm:      0 1px 2px rgba(0,0,0,0.05);
--tc-shadow:         0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
--tc-shadow-card:    0 1px 3px rgba(0,0,0,0.06);
--tc-shadow-card-hover: 0 4px 12px rgba(0,0,0,0.08);
--tc-shadow-elevated: 0 8px 24px rgba(0,0,0,0.10);
--tc-shadow-modal:   0 20px 60px rgba(0,0,0,0.15);
```

### Usage
- Cards resting: `--tc-shadow-card`
- Cards hover: `--tc-shadow-card-hover`
- Mega menu / drawer: `--tc-shadow-elevated`
- Modals: `--tc-shadow-modal`
- Buttons: none resting, `--tc-shadow-sm` on hover for primary

---

## Phase 7: Dark Mode Toggle JS

### Problem
CSS dark mode rules exist but NO JavaScript toggles them.

### Plan
Create minimal dark mode toggle script:
```javascript
(function() {
    const KEY = 'tc-theme';
    const body = document.body;
    const stored = localStorage.getItem(KEY);
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (stored === 'dark' || (!stored && prefersDark)) {
        body.classList.add('dark-mode');
    }

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.tc-theme-toggle');
        if (!btn) return;
        body.classList.toggle('dark-mode');
        localStorage.setItem(KEY, body.classList.contains('dark-mode') ? 'dark' : 'light');
    });
})();
```

Add to `textcraft-tools.js` or inline in footer. Insert toggle button in header.

---

## Phase 8: Mega Menu Header Cleanup

### Issues
- Duplicate `:root` blocks for header vars
- Header vars still named `--hdr-gold*`
- Mega panel uses `position: fixed` with `!important` overrides

### Plan
1. Merge header `:root` vars into main `:root` block
2. Rename `--hdr-gold*` → `--hdr-primary*`
3. Verify mega panel positioning works with new tokens
4. Test mobile drawer + accordion

---

## Phase 9: Footer Polish

### Current Issues
- CTA section uses `.tc-footer-cta-glow` (nearly invisible radial gradient)
- `.tc-btn-primary` in footer has different styles than `.tc-btn--primary` in tool cards

### Plan
- Remove footer CTA glow (unnecessary decoration)
- Ensure footer button classes match the main button system
- Verify dark mode overrides work for footer

---

## Phase 10: Responsive & Accessibility Audit

### Check
- All breakpoints: 360px, 480px, 768px, 1024px, 1200px, 1440px, 1920px
- `prefers-reduced-motion` disables all animations
- Focus-visible outlines on all interactive elements
- ARIA attributes on mega menu, accordion, toggle
- Screen reader text for icon-only elements

---

## Implementation Order

1. **Phase 1** — Variable rename (global find-replace)
2. **Phase 2** — Kill AI-slop (shadows, gradients, sheen)
3. **Phase 3** — Unified card system
4. **Phase 5** — Button system cleanup
5. **Phase 6** — Shadow system
6. **Phase 7** — Dark mode toggle JS
7. **Phase 8** — Header/mega menu cleanup
8. **Phase 9** — Footer polish
9. **Phase 4** — Typography refinement
10. **Phase 10** — Responsive + a11y audit

---

## Files Modified

| File | Changes |
|---|---|
| `assets/css/textcraft-tools.css` | Phases 1-6, 8-10 |
| `includes/class-textcraft-loader.php` | Phase 7 (enqueue dark mode script) |
| `includes/widgets/class-textcraft-base-widget.php` | Phase 7 (insert toggle button in header) |
| `assets/js/textcraft-dark-mode.js` | Phase 7 (new file) |

---

## Design Tokens Reference (Final)

```css
:root {
    /* Background */
    --tc-bg:             #ffffff;
    --tc-bg-soft:        #f8fafc;
    --tc-bg-card:        #ffffff;
    --tc-bg-elevated:    #ffffff;
    --tc-surface:        #f8fafc;
    --tc-surface-2:      #f1f5f9;
    --tc-surface-3:      #e2e8f0;

    /* Text */
    --tc-text:           #0f172a;
    --tc-text-primary:   #0f172a;
    --tc-text-secondary: #334155;
    --tc-text-muted:     #64748b;
    --tc-text-dim:       #94a3b8;

    /* Primary (Blue) */
    --tc-primary:        #2563eb;
    --tc-primary-hover:  #1d4ed8;
    --tc-primary-light:  #3b82f6;
    --tc-primary-dark:   #1e40af;
    --tc-primary-glow:   rgba(37,99,235,0.08);

    /* Warm Accent (Orange) — used sparingly */
    --tc-accent:         #f97316;
    --tc-accent-hover:   #ea580c;

    /* Semantic */
    --tc-success:        #16a34a;
    --tc-success-soft:   rgba(22,163,74,0.08);
    --tc-danger:         #dc2626;
    --tc-danger-soft:    rgba(220,38,38,0.08);
    --tc-warning:        #f59e0b;

    /* Border */
    --tc-border:         #e2e8f0;
    --tc-border-soft:    #f1f5f9;
    --tc-border-hover:   #cbd5e1;

    /* Border Radius */
    --tc-radius-sm:      4px;
    --tc-radius-md:      6px;
    --tc-radius-lg:      8px;
    --tc-radius-xl:      12px;

    /* Shadows */
    --tc-shadow-sm:      0 1px 2px rgba(0,0,0,0.05);
    --tc-shadow:         0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --tc-shadow-card:    0 1px 3px rgba(0,0,0,0.06);
    --tc-shadow-card-hover: 0 4px 12px rgba(0,0,0,0.08);
    --tc-shadow-elevated: 0 8px 24px rgba(0,0,0,0.10);
    --tc-shadow-modal:   0 20px 60px rgba(0,0,0,0.15);

    /* Typography */
    --tc-font-display:   -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --tc-font-body:      -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    --tc-font-mono:      ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, "Liberation Mono", monospace;

    /* Transitions */
    --tc-transition:     0.2s cubic-bezier(0.4, 0, 0.2, 1);
    --tc-transition-fast: 0.15s ease;
}
```
