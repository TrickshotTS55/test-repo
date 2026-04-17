# Design System – Pura Vida Real Estate

## Layout Principles
- Max content width: 1280px, centered
- Section padding: 80px top/bottom (desktop), 48px (tablet), 32px (mobile)
- White backgrounds for content sections, `#EEEEE8` for alternate sections
- Large images are the hero — never shrink them to add more text
- Always generous whitespace — luxury feels spacious

---

## Breakpoints
| Name    | Width    |
|---------|----------|
| Desktop | > 1024px |
| Tablet  | 768–1024px |
| Mobile  | < 768px  |

---

## Components

### CTA Button (Primary)
- Background: `#C9A84C`
- Text: `#22344B`, Inter, 11px, weight 600, uppercase, letter-spacing 0.15em
- Shape: pill/oval (border-radius: 50px)
- Padding: 16px 36px
- Hover: slightly darker gold, subtle shadow
- Example: "ARRANGE A VIEWING"

### CTA Button (Secondary / Outline)
- Background: transparent
- Border: 1px solid `#22344B` or white (depending on background)
- Text: same as primary but matching border color
- Example: "SEE HOW IT WORKS"

### Section Divider
- Thin gold line: 1px solid `#C9A84C`, width 60px, centered
- Used between sections or after headings

### Stat Block (e.g. property specs)
- Label: Inter, 11px, uppercase, letter-spacing 0.12em, color `#22344B` at 60% opacity
- Value: Cormorant Garamond, 28–36px, `#22344B`
- Separated by vertical 1px `#22344B` lines at 20% opacity

### Feature/Amenity Tag
- Checkmark icon in `#C9A84C`
- Text: Inter, 12px, uppercase, letter-spacing 0.12em, `#22344B`
- Displayed in 2-column grid

---

## Single Property Page Layout

### Hero Section
- Desktop: Large main photo (left, ~65% width) + 2×3 photo grid (right, ~35% width)
- Last grid cell: "SEE ALL PHOTOS" overlay button
- Tablet: Main photo full width, grid below in 3 columns
- Mobile: Main photo full width, horizontal scroll or 2-column grid

### Info Bar (below hero)
- Property title: Cormorant Garamond H1
- Left: "ARRANGE A VIEWING" button + "Personal advice in English, German and Russian"
- Right: Price, then stat blocks (Ref, Bedrooms, Bathrooms, m²)
- Mobile: Stack vertically, button full width

### Content Tabs
- DESCRIPTION | DOWNLOADS | OVERVIEW | FEATURES & AMENITIES
- Tab style: Inter, 11px, uppercase, letter-spacing 0.12em
- Active: gold underline or gold text
- Mobile: horizontal scroll tabs

### Content Section
- Desktop: Description (left ~55%) + Features & Amenities (right ~45%)
- Mobile: Description first, features below full width

---

## Responsive Rules
- Never use fixed pixel widths for containers on mobile
- Images: always `width: 100%`, `height: auto` or `object-fit: cover`
- Font sizes reduce by ~20% on mobile
- Padding/margin reduce by 40–50% on mobile
- Touch targets minimum 44px height on mobile
