# TextCraft Tools — Tool Pages Master Template

> Applies to: ALL 68 individual tool pages (`/tools/{slug}/`)
> This is the single most important redesign — it covers68 pages with one template
> Design inspiration: ConvertCase.net tool page

---

## Current State

Each tool page currently renders (via `TextCraft_Base_Widget`):
1. Hero section (badge + title + subtitle)
2. Tool card (textarea + buttons + output)
3. SEO content sections (intro, how-to, features, benefits, use-cases, why-choose)
4. FAQ accordion
5. Related tools grid

**Problems:**
- Hero takes too much space before the tool
- Tool card styling is inconsistent with ConvertCase.net's clean editor look
- SEO content sections are verbose and visually heavy
- FAQ items have excessive shadows
- Related tools look like separate cards, not integrated suggestions

---

## New Tool Page Architecture

```
1. HEADER (global)
2. BREADCRUMB NAV
   - Home > Category > Tool Name
   - Small, subtle, below header
3. HERO (compact)
   - Badge: category name (e.g., "Case Conversion")
   - H1: tool name (e.g., "Case Converter")
   - Subtitle: one-line description
   - NO trust badges here (they're on homepage)
4. TOOL CARD (editor wrapper)
   - White card on light surface background
   - Textarea input with toolbar (copy, clear, download)
   - Action buttons row (case buttons, conversion buttons, etc.)
   - Output/result area
   - Character/word count bar
5. SEO INTRO
   - 1-2 paragraphs max
   - Max-width: 800px, centered
6. HOW TO USE (collapsible)
   - 3-5 numbered steps
   - Compact, scannable
7. FEATURES GRID
   - 3-4 feature cards in a row
   - Icon + title + one-line description
8. USE CASES
   - Simple bulleted list
9. FAQ ACCORDION
   - 5-8 questions
   - Clean borders, no shadows
10. RELATED TOOLS
    - Horizontal row of 4-6 tool cards
    - Compact, icon + name format
11. FOOTER (global)
```

---

## Detailed Component Specs

### Breadcrumb

```
.tc-breadcrumb
  padding: 12px 24px
  max-width: 1200px
  margin: 0 auto
  font-size: 13px
  color: var(--tc-text-muted)

  a
    color: var(--tc-text-muted)
    text-decoration: none
    hover: color: var(--tc-primary)

  .tc-breadcrumb-sep
    margin: 0 8px
    color: var(--tc-text-dim)
```

### Compact Hero

```
.tc-hero--tool
  padding: 32px 24px 24px
  text-align: center

  .tc-hero-badge
    /* Same as homepage badge */
    margin-bottom: 12px          /* Reduced from 24px */

  h1
    font-size: clamp(24px, 4vw, 36px)   /* Slightly smaller than homepage */
    margin: 0 0 8px

  .tc-hero__subtitle
    font-size: 16px
    max-width: 500px
    margin: 0 auto
```

### Tool Card (Editor) — Premium Redesign

This is the most critical component. Must look like ConvertCase.net's editor:

```
.tc-tool-card
  background: var(--tc-surface)           /* #f8fafc — light blue-gray */
  border: 1px solid var(--tc-border)
  border-radius: var(--tc-radius-md)      /* 8px */
  max-width: 900px
  margin: 0 auto 48px
  padding: 0
  overflow: hidden

  /* Input area */
  .tc-textarea
    width: 100%
    min-height: 200px
    background: var(--tc-bg)              /* White */
    border: 0
    border-bottom: 1px solid var(--tc-border)
    border-radius: 0
    padding: 16px
    font-size: 15px
    line-height: 1.6
    resize: vertical
    outline: none

    focus: border-bottom-color: var(--tc-primary)

  /* Toolbar */
  .tc-tool-toolbar
    display: flex
    justify-content: space-between
    align-items: center
    padding: 10px 16px
    background: var(--tc-surface)
    border-bottom: 1px solid var(--tc-border)

    .tc-tool-actions
      display: flex
      gap: 8px

    .tc-tool-counts
      font-size: 13px
      font-weight: 600
      color: var(--tc-text-muted)

  /* Action buttons */
  .tc-btn-case
    background: var(--tc-bg)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-sm)
    padding: 8px 12px
    font-size: 13px
    font-weight: 600
    cursor: pointer
    transition: all 0.2s

    hover:
      background: var(--tc-primary)
      color: white
      border-color: var(--tc-primary)

  /* Output area */
  .tc-result-card
    background: var(--tc-bg)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-sm)
    margin: 12px 16px 16px
    padding: 14px 16px
    font-size: 15px
    line-height: 1.6
    min-height: 100px
```

### SEO Content (below tool)

```
.tc-seo-section
  max-width: 800px
  margin: 0 auto
  padding: 0 24px 48px

  /* Section headings */
  h2
    font-size: 22px
    font-weight: 700
    color: var(--tc-text)
    margin: 32px 0 12px
    letter-spacing: -0.015em

  h3
    font-size: 18px
    font-weight: 600
    color: var(--tc-text)
    margin: 24px 0 8px

  p
    font-size: 15px
    line-height: 1.7
    color: var(--tc-text-secondary)
    margin: 0 0 16px

  ul, ol
    padding-left: 20px
    margin: 0 0 16px

  li
    font-size: 15px
    line-height: 1.6
    color: var(--tc-text-secondary)
    margin-bottom: 8px

  /* Feature cards grid inside SEO */
  .tc-feature-grid
    display: grid
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr))
    gap: 16px
    margin: 16px 0 24px

  .tc-feature-card
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-md)
    padding: 20px 16px
    text-align: center

    .tc-feature-card__icon
      font-size: 28px
      margin-bottom: 8px

    .tc-feature-card__title
      font-size: 14px
      font-weight: 600
      margin: 0 0 4px

    .tc-feature-card__desc
      font-size: 13px
      color: var(--tc-text-muted)
      line-height: 1.5
```

### FAQ (Clean Accordion)

```
.tc-faq-accordion
  max-width: 800px
  margin: 0 auto
  padding: 0 24px 48px

  .tc-faq-item
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius)
    margin-bottom: 10px
    overflow: hidden
    background: var(--tc-bg-card)
    transition: border-color 0.2s

    &:hover
      border-color: var(--tc-border-hover)

  .tc-faq-question
    padding: 16px 20px
    font-size: 15px
    font-weight: 600
    color: var(--tc-text)
    cursor: pointer
    display: flex
    justify-content: space-between
    align-items: center

    &::after
      content: '+'
      font-size: 18px
      color: var(--tc-text-muted)
      transition: transform 0.2s

    is-open &::after
      transform: rotate(45deg)

  .tc-faq-answer
    padding: 0 20px 16px
    font-size: 15px
    line-height: 1.7
    color: var(--tc-text-secondary)
```

### Related Tools

```
.tc-related-tools
  max-width: 1200px
  margin: 0 auto
  padding: 0 24px 48px

  .tc-related-tools-list
    display: grid
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr))
    gap: 12px
    list-style: none
    padding: 0
    margin: 0

  .tc-related-tools-list li
    margin: 0

  .tc-related-tools-list a
    display: flex
    align-items: center
    gap: 10px
    padding: 12px 14px
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius)
    text-decoration: none
    color: var(--tc-text)
    font-size: 14px
    font-weight: 500
    transition: border-color 0.2s, box-shadow 0.2s

    hover:
      border-color: var(--tc-primary)
      box-shadow: var(--tc-shadow-card-hover)

    .tc-related-icon
      font-size: 20px
```

---

## Responsive Behavior

| Breakpoint | Tool Card | SEO Content | Feature Grid | Related Tools |
|---|---|---|---|---|
| `< 480px` | Full width, 16px padding | Single column | 1 column | 1 column |
| `480px – 768px` | Full width, 20px padding | Single column | 2 columns | 2 columns |
| `768px+` | Max-width 900px, centered | Max-width 800px | 3 columns | 3-4 columns |

---

## Key Design Decisions

1. **Hero is compact** — tool should be visible without scrolling
2. **Tool card is the star** — large, centered, white-on-gray contrast
3. **Toolbar is integrated** — not floating, part of the card
4. **Action buttons are minimal** — flat, near-square, subtle borders
5. **Output area is clean** — white card within the gray wrapper
6. **SEO content is scannable** — short paragraphs, clear headings, bulleted lists
7. **FAQ is understated** — borders only, no shadows, clean accordion
8. **Related tools are compact** — horizontal cards, icon + name format
9. **Breadcrumb provides context** — where am I, how do I go back
10. **Max-widths prevent stretching** — tool: 900px, content: 800px, grid: 1200px
