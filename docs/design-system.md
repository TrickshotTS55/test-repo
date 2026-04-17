# Design System – Pura Vida Real Estate

## Layout Principles
- Max content width: 1280px, centered
- Section padding: 80px top/bottom (desktop), 48px (tablet), 32px (mobile)
- Background: `#EEEEE8` (cream/sand) as page default, `#FFFFFF` for cards and content sections
- Large images and video are the hero — never shrink them to add more text
- Always generous whitespace — luxury feels spacious
- **Video-first mindset:** homepage and area guides should feature video prominently

---

## Breakpoints

| Name    | Width      |
|---------|------------|
| Desktop | > 1024px   |
| Tablet  | 768–1024px |
| Mobile  | < 768px    |

---

## Color Tokens

| Token          | Hex       | Usage                                        |
|----------------|-----------|----------------------------------------------|
| `--color-navy` | `#22344B` | Primary text, headings, nav, footer, buttons |
| `--color-sand` | `#EEEEE8` | Page background, alternate sections          |
| `--color-gold` | `#C9A84C` | CTA buttons, accents, dividers, checkmarks   |
| `--color-white`| `#FFFFFF` | Cards, overlays, clean content areas         |

---

## Typography Tokens

| Token              | Value                               |
|--------------------|-------------------------------------|
| `--font-heading`   | 'Cormorant Garamond', serif          |
| `--font-body`      | 'Inter', sans-serif                  |
| `--font-label`     | Inter, 11–13px, uppercase, 0.15em spacing |

### Typographic Scale (Desktop → Mobile)

| Element   | Desktop       | Mobile       | Font                |
|-----------|---------------|--------------|---------------------|
| H1        | 56–72px       | 36–44px      | Cormorant Garamond  |
| H2        | 40–48px       | 28–34px      | Cormorant Garamond  |
| H3        | 28–32px       | 22–26px      | Cormorant Garamond  |
| Body      | 16–18px       | 15–16px      | Inter               |
| Label/Cap | 11–13px       | 11px         | Inter, uppercase    |
| Price     | 32–40px       | 26–32px      | Cormorant Garamond  |

---

## Components

### CTA Button — Primary (Gold)
```css
background: #C9A84C;
color: #22344B;
font-family: 'Inter', sans-serif;
font-size: 11px;
font-weight: 600;
text-transform: uppercase;
letter-spacing: 0.15em;
border-radius: 50px;
padding: 16px 36px;
border: none;
cursor: pointer;
transition: background 0.2s ease;
```
Hover: `background: #b8933f`
Example: "ARRANGE A VIEWING"

### CTA Button — Secondary (Outline)
```css
background: transparent;
border: 1px solid #22344B;
color: #22344B;
/* same font/size/padding as primary */
```
On dark backgrounds: `border-color: #fff; color: #fff`
Example: "SEE HOW IT WORKS"

### Section Divider (Gold Line)
```css
width: 60px;
height: 1px;
background: #C9A84C;
margin: 24px auto;
```

### Stat Block (Property Specs)
- Label: Inter, 11px, uppercase, letter-spacing 0.12em, `rgba(34,52,75,0.6)`
- Value: Cormorant Garamond, 28–36px, `#22344B`
- Separator: `1px solid rgba(34,52,75,0.2)`, vertical

### Feature / Amenity Tag
- Checkmark `✓` in `#C9A84C`
- Text: Inter, 12px, uppercase, letter-spacing 0.12em, `#22344B`
- Layout: 2-column grid on desktop, 1-column on mobile

---

## Single Property Page Layout

### Hero — Photo Grid
- **Desktop:** Main photo left (~65% width) + 2×3 thumbnail grid right (~35%)
  - Last grid cell: "SEE ALL PHOTOS" overlay
- **Tablet:** Main photo full width, thumbnail grid below (3 columns)
- **Mobile:** Main photo full width, horizontal scroll row OR 2-column grid

### Info Bar (below hero)
- **Left:** H1 title, then "ARRANGE A VIEWING" button + "Personal advice in English, German and Russian"
- **Right:** Price (large), then stat row: Ref | Bedrooms | Bathrooms | m²
- **Mobile:** Title → Price → Button (full width) → Stats stacked

### Content Tabs
- Options: DESCRIPTION | DOWNLOADS | OVERVIEW | FEATURES & AMENITIES
- Style: Inter, 11px, uppercase, letter-spacing 0.12em
- Active state: gold underline + gold text color
- Mobile: horizontal scroll, no wrapping

### Content Section
- **Desktop:** Description (left ~55%) + Features & Amenities card (right ~42%)
- **Mobile:** Description first, features below at full width

---

## Video Integration
- Homepage hero: optional fullscreen video background (muted, looped)
- Area guides: YouTube embed, 16:9 ratio, full-width on mobile
- Property pages: optional video embed below photo gallery
- Video containers: always `aspect-ratio: 16/9`, `width: 100%`

---

## Responsive Rules
- Never use fixed pixel widths for containers
- Images: always `width: 100%`, `height: auto` or `object-fit: cover`
- Font sizes: scale down ~20% on mobile (see table above)
- Padding/margin: reduce ~40–50% on mobile
- Touch targets: minimum 44px height on mobile
- Two-column layouts collapse to single column below 768px
