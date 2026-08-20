# TextCraft Tools — All Tools Directory Redesign

> Page: `/free-online-text-tools/` (All Tools)
> Widget: `textcraft_all_tools_page`
> Priority: HIGH — second most visited page, tool discovery hub

---

## Current State

The All Tools page has:
1. Hero with badge + title + subtitle
2. Live search bar
3. 9 category sections with tool cards
4. "No results" empty state

**Problems:**
- Category headings are visually heavy
- Tool cards have inconsistent styling
- Search bar doesn't stand out enough
- No visual category indicators (icons, colors)
- Grid spacing is inconsistent

---

## New All Tools Architecture

```
1. HEADER (global)
2. HERO (compact)
   - Badge: "All Tools"
   - H1: "74 Free Online Text Tools"
   - Subtitle: "Browse our complete collection of free browser-based tools."
3. SEARCH BAR (prominent)
   - Full-width search input
   - Large, centered, with icon
   - Placeholder: "Search tools..."
   - Live filtering as you type
4. CATEGORY TABS / PILLS (NEW)
   - Horizontal scrollable row of category pills
   - "All" | "PDF" | "Image" | "Text" | "Random" | etc.
   - Click to filter (smooth scroll to section)
5. CATEGORY SECTIONS
   - Category heading with icon + name + tool count
   - Grid of tool cards
   - Clean, consistent card styling
6. NO RESULTS STATE
   - Friendly empty state with illustration
   - "No tools found matching your search"
```

---

## Component Specs

### Search Bar

```
.tc-atp-search-wrap
  max-width: 600px
  margin: 0 auto 32px
  position: relative

  .tc-atp-search-icon
    position: absolute
    left: 16px
    top: 50%
    transform: translateY(-50%)
    color: var(--tc-text-muted)
    font-size: 18px
    pointer-events: none

  .tc-atp-search
    width: 100%
    padding: 14px 16px 14px 48px
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: var(--tc-radius-md)
    font-size: 16px
    color: var(--tc-text)
    outline: none
    transition: border-color 0.2s, box-shadow 0.2s

    &::placeholder
      color: var(--tc-text-dim)

    &:focus
      border-color: var(--tc-primary)
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1)
```

### Category Heading

```
.tc-atp-category
  margin-bottom: 32px

  .tc-atp-category-header
    display: flex
    align-items: center
    gap: 10px
    margin-bottom: 16px

    .tc-atp-category-icon
      font-size: 24px

    .tc-atp-category-name
      font-size: 20px
      font-weight: 700
      color: var(--tc-text)

    .tc-atp-category-count
      background: var(--tc-surface-2)
      border-radius: 99px
      padding: 2px 10px
      font-size: 12px
      font-weight: 600
      color: var(--tc-text-muted)
```

### Tool Card (Directory)

```
.tc-atp-card
  display: flex
  flex-direction: column
  gap: 10px
  background: var(--tc-bg-card)
  border: 1px solid var(--tc-border)
  border-radius: var(--tc-radius-md)
  padding: 20px
  text-decoration: none
  transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s

  &:hover
    border-color: var(--tc-border-hover)
    box-shadow: var(--tc-shadow-card-hover)
    transform: translateY(-2px)

  .tc-atp-card-icon
    width: 44px
    height: 44px
    background: var(--tc-surface-2)
    border-radius: var(--tc-radius)
    display: flex
    align-items: center
    justify-content: center
    font-size: 20px

  .tc-atp-card-name
    font-size: 16px
    font-weight: 600
    color: var(--tc-text)

  .tc-atp-card-desc
    font-size: 13px
    color: var(--tc-text-muted)
    line-height: 1.5
```

### Category Pills (NEW)

```
.tc-atp-pills
  display: flex
  gap: 8px
  overflow-x: auto
  padding: 0 24px 24px
  max-width: 1200px
  margin: 0 auto
  scrollbar-width: none

  &::-webkit-scrollbar
    display: none

  .tc-atp-pill
    flex-shrink: 0
    padding: 8px 16px
    background: var(--tc-bg-card)
    border: 1px solid var(--tc-border)
    border-radius: 99px
    font-size: 14px
    font-weight: 500
    color: var(--tc-text-secondary)
    cursor: pointer
    transition: all 0.2s
    white-space: nowrap

    &:hover
      border-color: var(--tc-primary)
      color: var(--tc-primary)

    &.is-active
      background: var(--tc-primary)
      color: white
      border-color: var(--tc-primary)
```

---

## Responsive Behavior

| Breakpoint | Grid | Search | Pills |
|---|---|---|---|
| `< 480px` | 1 column | Full width | Horizontal scroll |
| `480px – 768px` | 2 columns | Full width | Horizontal scroll |
| `768px+` | 3-4 columns | Max-width 600px | Centered row |

---

## Key Design Decisions

1. **Search is prominent** — large, centered, with icon
2. **Category pills allow quick filtering** — no need to scroll through all categories
3. **Tool cards are compact** — icon + name + short description
4. **Category headings have icons** — visual category identification
5. **Counts shown** — "12 tools" badge next to category name
6. **Consistent card styling** — same border-radius, padding, hover as homepage cards
7. **No glow effects, no sheen** — clean flat cards
