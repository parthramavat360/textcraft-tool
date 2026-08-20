# TextCraft Tools — Master Design System

> Premium redesign inspired by ConvertCase.net's clean, professional aesthetic.
> All changes implemented via CSS in `textcraft-tools.css` — no Tailwind, no new plugin.

---

## Design Philosophy

**"Restraint sells quality."** — ConvertCase.net

- Clean, minimal, professional — "enterprise SaaS" feel
- Constrained color palette: navy/blue primary + orange accent
- Consistent 8px grid spacing system
- Two-font pairing: display font for headings + body font for text
- Subtle shadows, near-square corners, generous whitespace
- Dark mode with warm tones (not pure black)
- Nothing is loud — visual hierarchy through size and weight, not color

---

## Color Palette

### Light Mode

| Token | Value | Usage |
|---|---|---|
| `--tc-bg` | `#ffffff` | Page background |
| `--tc-bg-card` | `#ffffff` | Card/panel backgrounds |
| `--tc-surface` | `#f8fafc` | Editor wrapper, about cards bg |
| `--tc-surface-2` | `#f1f5f9` | Input backgrounds, button bg |
| `--tc-surface-3` | `#e2e8f0` | Hover states on buttons |
| `--tc-text` | `#0f172a` | Primary text (very dark navy) |
| `--tc-text-secondary` | `#475569` | Secondary/muted text |
| `--tc-text-muted` | `#64748b` | Lighter muted text |
| `--tc-text-dim` | `#94a3b8` | Disabled/hint text |
| `--tc-primary` | `#2563eb` | Primary blue (links, CTAs, focus rings) |
| `--tc-primary-dark` | `#1d4ed8` | Darker blue for hover |
| `--tc-primary-light` | `#3b82f6` | Lighter blue for icons, badges |
| `--tc-accent` | `#f97316` | Orange accent (hover, selection, highlights) |
| `--tc-border` | `#e2e8f0` | Default borders |
| `--tc-border-hover` | `#cbd5e1` | Hover border color |
| `--tc-border-focus` | `#2563eb` | Focus ring color |
| `--tc-shadow-card` | `0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)` | Card resting shadow |
| `--tc-shadow-card-hover` | `0 4px 12px rgba(0,0,0,0.08)` | Card hover shadow |
| `--tc-shadow-elevated` | `0 8px 24px rgba(0,0,0,0.12)` | Elevated panels |
| `--tc-radius-sm` | `4px` | Small elements (badges, inputs) |
| `--tc-radius` | `6px` | Default radius |
| `--tc-radius-md` | `8px` | Medium cards |
| `--tc-radius-lg` | `12px` | Large cards, modals |

### Dark Mode

| Token | Value | Usage |
|---|---|---|
| `--tc-bg` | `#0f172a` | Page background |
| `--tc-bg-card` | `#1e293b` | Card backgrounds |
| `--tc-surface` | `#1e293b` | Editor wrapper |
| `--tc-surface-2` | `#334155` | Input backgrounds |
| `--tc-surface-3` | `#475569` | Hover states |
| `--tc-text` | `#f1f5f9` | Primary text |
| `--tc-text-secondary` | `#cbd5e1` | Secondary text |
| `--tc-text-muted` | `#94a3b8` | Muted text |
| `--tc-text-dim` | `#64748b` | Disabled text |
| `--tc-primary` | `#3b82f6` | Primary blue |
| `--tc-primary-dark` | `#2563eb` | Darker blue |
| `--tc-accent` | `#fb923c` | Orange accent |
| `--tc-border` | `#334155` | Default borders |
| `--tc-border-hover` | `#475569` | Hover borders |

---

## Typography

### Font Families

| Token | Font | Usage |
|---|---|---|
| `--tc-font-display` | `'Inter', 'Segoe UI', system-ui, sans-serif` | Headings, nav, labels |
| `--tc-font-body` | `'Inter', 'Segoe UI', system-ui, sans-serif` | Body text, inputs, descriptions |
| `--tc-font-mono` | `'JetBrains Mono', 'Fira Code', ui-monospace, monospace` | Code examples, output |

> **Note:** Inter is loaded from Google Fonts (already in use). Two-weight pairing (600 for display, 400 for body).

### Type Scale

| Element | Size | Weight | Line Height | Letter Spacing |
|---|---|---|---|---|
| h1 (hero) | `clamp(28px, 4vw, 40px)` | 700 | 1.15 | -0.02em |
| h2 (section) | `clamp(22px, 3vw, 30px)` | 700 | 1.2 | -0.015em |
| h3 (card title) | `18px` | 600 | 1.3 | -0.01em |
| Body | `16px` | 400 | 1.6 | normal |
| Small / label | `14px` | 500 | 1.4 | normal |
| Caption | `12px` | 500 | 1.3 | 0.02em |
| Badge | `11px` | 700 | 1 | 0.05em |

### Key Rules
- Headings: `font-family: var(--tc-font-display); font-weight: 700; letter-spacing: -0.02em;`
- Body: `font-family: var(--tc-font-body); font-weight: 400; line-height: 1.6;`
- Code/monospace: `font-family: var(--tc-font-mono); font-size: 14px;`
- All headings use `color: var(--tc-text)` (dark navy in light, warm white in dark)

---

## Spacing System (8px Grid)

| Token | Value | Usage |
|---|---|---|
| `--sp-xs` | `4px` | Tight gaps (badge padding, icon gaps) |
| `--sp-sm` | `8px` | Small gaps (between buttons, tags) |
| `--sp-md` | `12px` | Medium gaps (card internal spacing) |
| `--sp` | `16px` | Base unit (card padding, section gaps) |
| `--sp-lg` | `24px` | Large gaps (section padding, card padding) |
| `--sp-xl` | `32px` | Extra large (section margins) |
| `--sp-2xl` | `48px` | Hero padding, major section spacing |
| `--sp-3xl` | `64px` | Page-level section dividers |

### Key Spacings

| Context | Value |
|---|---|
| Container max-width | `1200px` |
| Container padding | `16px` mobile → `24px` desktop |
| Section margin-bottom | `48px` |
| Card padding | `20px` (compact) → `28px` (standard) |
| Card gap in grid | `16px` |
| Hero padding | `48px 0 32px` |
| Tool wrapper padding | `24px` |
| Between tool and SEO content | `48px` |

---

## Component Patterns

### 1. Tool Card (Editor Wrapper)

The centerpiece of every tool page. Inspired by ConvertCase.net's `.editor` pattern:

```
.tc-tool-card
  background: var(--tc-surface)           /* Light blue-gray (#f8fafc) */
  border: 1px solid var(--tc-border)      /* Subtle border */
  border-radius: var(--tc-radius-md)      /* 8px corners */
  padding: 0
  overflow: hidden

  .tc-textarea (input)
    background: var(--tc-bg)              /* White on light surface */
    border: 1px solid var(--tc-border)
    border-radius: 0
    min-height: 180px
    padding: 14px 16px
    font-size: 15px

  .tc-btn-case (action buttons)
    background: var(--tc-surface-2)       /* Light button bg */
    border: 0
    border-radius: var(--tc-radius-sm)    /* 4px */
    padding: 8px 12px
    font-weight: 600
    font-size: 14px
    hover: background inverts to dark

  .tc-result-card (output)
    background: var(--tc-bg)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-sm)
    padding: 14px 16px
```

### 2. Action Buttons (Toolbar)

```
.tc-btn
  background: var(--tc-surface-2)
  border: 1px solid var(--tc-border)
  border-radius: var(--tc-radius-sm)      /* 4px — near square */
  padding: 8px 16px
  font-weight: 600
  font-size: 14px
  color: var(--tc-text)
  transition: background 0.2s, color 0.2s

  hover:
    background: var(--tc-primary)
    color: white
    border-color: var(--tc-primary)
```

### 3. Primary CTA Button

```
.tc-btn-primary
  background: var(--tc-primary)           /* #2563eb */
  color: white
  border: 0
  border-radius: var(--tc-radius-sm)
  padding: 10px 24px
  font-weight: 600
  font-size: 15px
  transition: background 0.2s

  hover: background: var(--tc-primary-dark)  /* #1d4ed8 */
```

### 4. Badge / Tag

```
.tc-badge
  display: inline-flex
  align-items: center
  gap: 6px
  background: var(--tc-surface-2)
  border-radius: var(--tc-radius-sm)     /* 4px — near square */
  padding: 4px 10px
  font-size: 12px
  font-weight: 600
  color: var(--tc-text-secondary)
  letter-spacing: 0.03em
```

### 5. Section Title

```
.tc-section-title
  font-family: var(--tc-font-display)
  font-size: clamp(22px, 3vw, 30px)
  font-weight: 700
  color: var(--tc-text)
  letter-spacing: -0.015em
  margin: 0 0 8px
```

### 6. SEO Content Section

```
.tc-seo-section
  max-width: 800px
  margin: 0 auto

  h2: font-size: 22px, font-weight: 700, margin-bottom: 12px
  h3: font-size: 18px, font-weight: 600, margin-bottom: 8px
  p:  font-size: 15px, line-height: 1.7, color: var(--tc-text-secondary)
  ul: padding-left: 20px, margin-bottom: 16px
  li: margin-bottom: 8px, font-size: 15px
```

### 7. FAQ Accordion

```
.tc-faq-item
  background: var(--tc-bg-card)
  border: 1px solid var(--tc-border)
  border-radius: var(--tc-radius)         /* 6px */
  margin-bottom: 12px
  overflow: hidden
  transition: border-color 0.2s

  .tc-faq-question
    padding: 16px 20px
    font-weight: 600
    cursor: pointer

  .tc-faq-answer
    padding: 0 20px 16px
    color: var(--tc-text-secondary)
    font-size: 15px
    line-height: 1.7
```

---

## Grid Patterns

| Context | Grid | Gap |
|---|---|---|
| Tool pages (features grid) | `repeat(auto-fill, minmax(250px, 1fr))` | `16px` |
| Homepage tool cards | `repeat(auto-fill, minmax(260px, 1fr))` | `16px` |
| All Tools directory | `repeat(auto-fill, minmax(260px, 1fr))` | `16px` |
| Category grid | `repeat(auto-fill, minmax(240px, 1fr))` | `16px` |
| Feature cards | `repeat(auto-fill, minmax(280px, 1fr))` | `20px` |
| Footer columns | `repeat(5, 1fr)` desktop, stack mobile | `24px` |

---

## Responsive Breakpoints

| Breakpoint | Width | Layout |
|---|---|---|
| Mobile | `< 480px` | Single column, reduced padding |
| Tablet | `480px – 768px` | 2-column grids |
| Desktop | `768px – 1200px` | Full layout, 3-4 column grids |
| Wide | `> 1200px` | Centered container, max-width 1200px |

---

## Dark Mode Implementation

- Toggle: `textcraft-dark-mode.js` toggles `data-theme="dark"` on `<html>`
- CSS: `[data-theme="dark"]` selector block at end of `textcraft-tools.css`
- Tokens: All color tokens are overridden in the dark mode block
- Transition: `transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease`
- System preference: `@media (prefers-color-scheme: dark)` with `html:not(.light)` fallback
- Persistence: `localStorage('tc-theme')`

---

## Design Rules (Do / Don't)

### Do
- Use `var(--tc-*)` tokens for ALL colors, shadows, radii
- Use 8px grid spacing
- Use `font-family: var(--tc-font-display)` for headings
- Use `letter-spacing: -0.02em` on large headings
- Use subtle shadows: `0 1px 3px` to `0 4px 12px` max
- Use `border-radius: 4px` for small elements, `8px` for cards
- Use `transition: 0.2s` for all interactive states
- Use `font-weight: 600-700` for headings, `400` for body
- Use generous whitespace between sections (48px+)

### Don't
- Don't use neon/glow effects
- Don't use colored shadows (no blue/purple/orange shadow tints)
- Don't use gradients on backgrounds (flat colors only, except subtle body gradient)
- Don't use `text-shadow` or `box-shadow` with color
- Don't use `letter-spacing` on body text (only headings/badges)
- Don't use `transform: scale()` on hover (use `translateY(-2px)` only)
- Don't use gold, neon, or overly saturated colors
- Don't use `!important` unless overriding Elementor
- Don't use `Playfair Display` or decorative fonts

---

## Page Architecture

| Page | Layout | Key Sections |
|---|---|---|
| **Homepage** | Hero → Tool Preview → Features → Categories → Tools Grid → CTA → FAQ | Marketing + conversion |
| **Tool Pages (×68)** | Hero → Tool Card → SEO Content → Features Grid → How-To → FAQ → Related Tools | Tool + SEO content |
| **All Tools** | Hero → Search → Category Sections → Tool Cards | Directory/discovery |
| **Static Pages** | Hero → Content → Related Links | Informational |
| **Global** | Header (sticky) → Content → Footer (3-tier) | Navigation + brand |

---

## File Structure

```
textcraft-tools.css
├── :root (design tokens)
├── [data-theme="dark"] (dark mode tokens)
├── Global reset / base styles
├── Header / Navigation
├── Mega Menu
├── Mobile Drawer
├── Hero sections
├── Tool Card (editor wrapper)
├── Case buttons / action buttons
├── Textarea / inputs
├── Result cards
├── Feature cards / icons
├── SEO content sections
├── FAQ accordion
├── Homepage sections
├── All Tools directory
├── Static page styles
├── Footer (3-tier)
├── Dark mode component overrides
├── Responsive (mobile/tablet/desktop)
├── Reduced motion
└── Focus / accessibility
```
