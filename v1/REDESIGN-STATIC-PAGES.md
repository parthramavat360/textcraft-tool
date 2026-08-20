# TextCraft Tools — Static Pages Redesign

> Applies to: About, Contact, Accessibility, DMCA, Legal pages, Blog
> These are content pages with no tool widgets
> Priority: MEDIUM — informational pages, lower traffic

---

## Common Layout Pattern

All static pages share the same structure:

```
1. HEADER (global)
2. BREADCRUMB
3. HERO (page title)
4. CONTENT (rich text)
5. RELATED LINKS (optional)
6. FOOTER (global)
```

---

## Page-Specific Specs

### About Us (`/about-us/`)

```
.tc-page-about
  .tc-hero--page
    padding: 48px 24px 32px
    text-align: center

    h1: "About TextCraft Tools"
    subtitle: "Free browser-based tools for everyone."

  .tc-page-content
    max-width: 800px
    margin: 0 auto
    padding: 0 24px 48px

    /* Two-column layout for team/values section */
    .tc-about-grid
      display: grid
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))
      gap: 24px
      margin: 32px 0

    .tc-about-card
      background: var(--tc-bg-card)
      border: 1px solid var(--tc-border)
      border-radius: var(--tc-radius-md)
      padding: 24px

      .tc-about-card-icon
        font-size: 32px
        margin-bottom: 12px

      h3
        font-size: 17px
        font-weight: 600
        margin: 0 0 8px

      p
        font-size: 14px
        color: var(--tc-text-secondary)
        line-height: 1.7
        margin: 0
```

### Contact Us (`/contact-us/`)

```
.tc-page-contact
  .tc-hero--page: "Contact Us"

  .tc-contact-grid
    display: grid
    grid-template-columns: 1fr 1fr
    gap: 32px
    max-width: 900px
    margin: 0 auto
    padding: 0 24px 48px

    /* Contact form */
    .tc-contact-form
      .tc-form-group
        margin-bottom: 16px

        label
          display: block
          font-size: 14px
          font-weight: 600
          color: var(--tc-text)
          margin-bottom: 6px

        input, textarea, select
          width: 100%
          padding: 10px 12px
          background: var(--tc-bg-card)
          border: 1px solid var(--tc-border)
          border-radius: var(--tc-radius)
          font-size: 14px
          color: var(--tc-text)
          outline: none
          transition: border-color 0.2s

          &:focus
            border-color: var(--tc-primary)

    /* Contact info */
    .tc-contact-info
      .tc-contact-card
        background: var(--tc-surface)
        border-radius: var(--tc-radius-md)
        padding: 24px

@media (max-width: 768px) {
  .tc-contact-grid { grid-template-columns: 1fr; }
}
```

### Accessibility Statement (`/accessibility-statement/`)

```
.tc-page-accessibility
  .tc-hero--page: "Accessibility Statement"

  .tc-page-content
    max-width: 800px
    margin: 0 auto
    padding: 0 24px 48px

    /* Standard rich text content */
    h2: 22px, font-weight 700, margin 32px 0 12px
    h3: 18px, font-weight 600, margin 24px 0 8px
    p: 15px, line-height 1.7, color var(--tc-text-secondary)
    ul: padding-left 20px
    li: 15px, margin-bottom 8px

    /* Compliance badge */
    .tc-a11y-badge
      display: inline-flex
      align-items: center
      gap: 8px
      background: var(--tc-surface-2)
      border: 1px solid var(--tc-border)
      border-radius: var(--tc-radius)
      padding: 12px 16px
      margin: 16px 0
      font-size: 14px
      font-weight: 500
```

### DMCA Policy (`/dmca-policy/`)

```
.tc-page-dmca
  /* Same layout as Accessibility */
  .tc-hero--page: "DMCA Policy"
  .tc-page-content: standard rich text

  /* Contact box for DMCA notices */
  .tc-dmca-contact
    background: var(--tc-surface)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-md)
    padding: 24px
    margin: 24px 0
```

### Legal Pages (Privacy, Terms, Cookie, etc.)

```
.tc-page-legal
  /* All legal pages share the same layout */
  .tc-hero--page: [page title]
  .tc-page-content: standard rich text
    max-width: 800px
    margin: 0 auto

    /* Last reviewed date */
    .tc-last-reviewed
      font-size: 13px
      color: var(--tc-text-muted)
      margin-bottom: 24px
      padding-bottom: 16px
      border-bottom: 1px solid var(--tc-border)
```

### Blog Archive (`/blog/`)

```
.tc-blog-archive
  .tc-hero--page: "Blog"

  .tc-blog-grid
    display: grid
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr))
    gap: 24px
    max-width: 1200px
    margin: 0 auto
    padding: 0 24px 48px

  .tc-blog-card
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-md)
    overflow: hidden
    text-decoration: none
    transition: border-color 0.2s, box-shadow 0.2s

    &:hover
      border-color: var(--tc-border-hover)
      box-shadow: var(--tc-shadow-card-hover)

    .tc-blog-card-image
      width: 100%
      height: 180px
      object-fit: cover

    .tc-blog-card-body
      padding: 20px

      .tc-blog-card-date
        font-size: 12px
        color: var(--tc-text-muted)
        margin-bottom: 8px

      h3
        font-size: 17px
        font-weight: 600
        color: var(--tc-text)
        margin: 0 0 8px

      p
        font-size: 14px
        color: var(--tc-text-secondary)
        line-height: 1.6
        margin: 0
```

---

## Key Design Decisions

1. **Consistent page hero** — all pages have the same compact hero pattern
2. **Max-width 800px for content** — optimal reading width
3. **Rich text is well-typed** — proper heading hierarchy, line-height 1.7, muted text
4. **Cards are minimal** — white background, subtle border, clean corners
5. **No decorative elements** — content is king on these pages
6. **Form inputs are clean** — same styling as tool inputs
7. **Legal pages have "Last reviewed" date** — trust indicator
8. **Blog cards follow the same pattern** — icon + content + hover effect
