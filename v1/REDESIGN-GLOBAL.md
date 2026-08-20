# TextCraft Tools — Global Elements Redesign

> Applies to: Header, Mega Menu, Footer — appear on ALL pages
> Priority: HIGH — these define the site's visual identity

---

## 1. Header / Navigation

### Current State
- Sticky header with logo, nav links, dark mode toggle, hamburger
- Mega menu dropdown on "All Tools"
- Mobile: hamburger → off-canvas drawer

### New Design

```
.site-header
  position: sticky
  top: 0
  z-index: 1000
  background: var(--tc-bg)                 /* White */
  border-bottom: 1px solid var(--tc-border)
  transition: transform 0.3s ease, box-shadow 0.2s

  /* Scrolled state */
  &.scrolled
    box-shadow: 0 1px 3px rgba(0,0,0,0.06)

  .header-inner
    max-width: 1200px
    margin: 0 auto
    display: flex
    align-items: center
    justify-content: space-between
    padding: 0 24px
    height: 56px                    /* Fixed height, not min-height */

  /* Logo */
  .site-logo
    display: flex
    align-items: center
    gap: 8px
    text-decoration: none

    .logo-icon
      font-size: 24px

    .logo-text
      font-family: var(--tc-font-display)
      font-size: 18px
      font-weight: 700
      color: var(--tc-text)
      letter-spacing: -0.02em

  /* Nav links */
  .nav-links
    display: flex
    align-items: center
    gap: 4px
    list-style: none
    margin: 0
    padding: 0

    li a, li > span
      display: flex
      align-items: center
      gap: 4px
      padding: 8px 12px
      font-size: 14px
      font-weight: 500
      color: var(--tc-text-secondary)
      text-decoration: none
      border-radius: var(--tc-radius)
      transition: color 0.2s, background 0.2s

      &:hover
        color: var(--tc-text)
        background: var(--tc-surface-2)

      &.active
        color: var(--tc-primary)

  /* Dark mode toggle */
  .tc-theme-toggle
    width: 36px
    height: 36px
    border-radius: var(--tc-radius)
    border: 1px solid var(--tc-border)
    background: var(--tc-bg-card)
    cursor: pointer
    display: flex
    align-items: center
    justify-content: center
    font-size: 16px
    transition: background 0.2s, border-color 0.2s

    &:hover
      background: var(--tc-surface-2)
      border-color: var(--tc-border-hover)

  /* Hamburger (mobile) */
  .hamburger
    display: none
    width: 36px
    height: 36px
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius)
    background: var(--tc-bg-card)
    cursor: pointer
    flex-direction: column
    align-items: center
    justify-content: center
    gap: 4px
    padding: 8px

    .hamburger__bar
      width: 16px
      height: 2px
      background: var(--tc-text)
      border-radius: 1px
      transition: transform 0.2s, opacity 0.2s

    &[aria-expanded="true"] .hamburger__bar:nth-child(1)
      transform: rotate(45deg) translate(2px, 4px)
    &[aria-expanded="true"] .hamburger__bar:nth-child(2)
      opacity: 0
    &[aria-expanded="true"] .hamburger__bar:nth-child(3)
      transform: rotate(-45deg) translate(2px, -4px)

@media (max-width: 768px) {
  .site-header .nav-links { display: none; }
  .site-header .hamburger { display: flex; }
}
```

---

## 2. Mega Menu

### Current State
- Dropdown panel on "All Tools" hover
- 5-column layout with tool links
- Shows on desktop, hidden on mobile (uses drawer instead)

### New Design

```
.mega-panel, .tc-mega-panel
  position: absolute
  top: 100%
  left: 0
  right: 0
  background: var(--tc-bg-card)
  border: 1px solid var(--tc-border)
  border-top: 2px solid var(--tc-primary)
  border-radius: 0 0 var(--tc-radius-md) var(--tc-radius-md)
  box-shadow: 0 8px 24px rgba(0,0,0,0.08)
  padding: 24px
  z-index: 100

  .mega-columns, .tc-mega-columns
    display: grid
    grid-template-columns: repeat(5, 1fr)
    gap: 24px
    max-width: 1200px
    margin: 0 auto

  .mega-col, .tc-mega-col
    .mega-col__heading, .tc-mega-category
      font-size: 13px
      font-weight: 700
      color: var(--tc-text)
      text-transform: uppercase
      letter-spacing: 0.05em
      margin-bottom: 10px
      padding-bottom: 8px
      border-bottom: 1px solid var(--tc-border)

    .mega-tool-link, .tc-mega-link
      display: block
      padding: 5px 0
      font-size: 13px
      color: var(--tc-text-secondary)
      text-decoration: none
      transition: color 0.15s

      &:hover
        color: var(--tc-primary)

@media (max-width: 1024px) {
  .mega-panel { display: none !important; }
}
```

---

## 3. Mobile Drawer

### Current State
- Off-canvas drawer slides from right
- Accordion categories
- Dark overlay

### New Design

```
.mobile-drawer
  position: fixed
  top: 0
  right: 0
  width: 300px
  height: 100vh
  background: var(--tc-bg-card)
  z-index: 2000
  transform: translateX(100%)
  transition: transform 0.3s ease
  overflow-y: auto
  padding: 16px

  &.is-open
    transform: translateX(0)

  .drawer-header
    display: flex
    justify-content: space-between
    align-items: center
    margin-bottom: 16px
    padding-bottom: 12px
    border-bottom: 1px solid var(--tc-border)

  .drawer-close
    width: 32px
    height: 32px
    border-radius: var(--tc-radius)
    border: 1px solid var(--tc-border)
    background: none
    cursor: pointer
    font-size: 16px
    display: flex
    align-items: center
    justify-content: center

.mobile-overlay
  position: fixed
  inset: 0
  background: rgba(0,0,0,0.3)
  z-index: 1999
  opacity: 0
  pointer-events: none
  transition: opacity 0.3s

  &.is-open
    opacity: 1
    pointer-events: auto
```

---

## 4. Footer (3-Tier)

### Current State
- CTA bar → 5-column grid → Bottom bar with copyright + social

### New Design

```
.tc-footer
  background: var(--tc-surface)           /* Subtle background */
  border-top: 1px solid var(--tc-border)

  /* Tier 1: CTA Bar */
  .tc-footer-cta
    padding: 32px 24px
    text-align: center
    border-bottom: 1px solid var(--tc-border)

    .tc-footer-cta-title
      font-size: 20px
      font-weight: 700
      color: var(--tc-text)
      margin: 0 0 16px

    .tc-footer-cta-buttons
      display: flex
      gap: 12px
      justify-content: center

  /* Tier 2: Main Grid */
  .tc-footer-grid
    display: grid
    grid-template-columns: 2fr repeat(4, 1fr)
    gap: 32px
    max-width: 1200px
    margin: 0 auto
    padding: 40px 24px

    /* Brand column */
    .tc-footer-brand
      .tc-footer-logo-text
        font-size: 18px
        font-weight: 700
        color: var(--tc-text)
        margin-bottom: 12px

      .tc-footer-brand-desc
        font-size: 14px
        color: var(--tc-text-secondary)
        line-height: 1.6

    /* Link columns */
    .tc-footer-col
      .tc-footer-title
        font-size: 13px
        font-weight: 700
        color: var(--tc-text)
        text-transform: uppercase
        letter-spacing: 0.05em
        margin-bottom: 12px

      .tc-footer-links
        list-style: none
        padding: 0
        margin: 0

        li
          margin-bottom: 6px

        a
          font-size: 14px
          color: var(--tc-text-secondary)
          text-decoration: none
          transition: color 0.15s

          &:hover
            color: var(--tc-primary)

  /* Tier 3: Bottom Bar */
  .tc-footer-bottom
    padding: 16px 24px
    border-top: 1px solid var(--tc-border)
    display: flex
    justify-content: space-between
    align-items: center
    max-width: 1200px
    margin: 0 auto

    .tc-footer-copyright
      font-size: 13px
      color: var(--tc-text-muted)

    .tc-footer-social
      display: flex
      gap: 12px

      .tc-social-icon
        width: 32px
        height: 32px
        border-radius: var(--tc-radius)
        border: 1px solid var(--tc-border)
        display: flex
        align-items: center
        justify-content: center
        color: var(--tc-text-secondary)
        text-decoration: none
        font-size: 14px
        transition: all 0.2s

        &:hover
          border-color: var(--tc-primary)
          color: var(--tc-primary)

@media (max-width: 768px) {
  .tc-footer-grid {
    grid-template-columns: 1fr 1fr;
  }
  .tc-footer-brand {
    grid-column: 1 / -1;
  }
  .tc-footer-bottom {
    flex-direction: column;
    gap: 12px;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .tc-footer-grid {
    grid-template-columns: 1fr;
  }
}
```

---

## Key Design Decisions

1. **Header is minimal** — 56px height, no heavy backgrounds, clean border
2. **Mega menu has blue top border** — visual indicator of the dropdown
3. **Mobile drawer is right-side** — standard pattern, easy thumb reach
4. **Footer is understated** — subtle background, clean grid, no heavy colors
5. **Social icons are bordered** — not filled, subtle interaction
6. **All transitions are 0.2-0.3s** — smooth but not sluggish
7. **Dark mode toggle is a simple button** — icon only, no pill/toggle switch
