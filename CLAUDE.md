# Pura Vida Real Estate – Claude Context

## Project Overview
Premium real estate website for Davy & Marina Siger. Focus: international buyers searching for holiday homes, retirement, or investment property on Costa Blanca South and Costa Cálida, Spain. Strategic partner: InSun Properties (XML feed source).

**Live site:** WordPress + Elementor Pro + Hello Elementor theme  
**Languages:** English (primary), German, Russian  
**Owner:** Davy (tech/marketing) & Marina (client ops)

---

## Current Priority
Improving the **Single Property Template** and **Archive/Properties Template** in Elementor. Data comes from an XML feed imported via WP All Import Pro into ACF fields.

---

## Tech Stack
- WordPress
- Elementor Pro (page builder — all layouts built here)
- Hello Elementor (minimal base theme)
- Advanced Custom Fields (ACF) — stores all property data
- WP All Import Pro — imports XML feed into ACF fields
- Rank Math SEO
- WP Fastest Cache

---

## Key Docs
- `docs/brand.md` — logo, colors, typography, tone
- `docs/design-system.md` — spacing, components, responsive rules
- `docs/property-import.md` — XML feed structure, ACF fields, import logic
- `docs/content-guidelines.md` — writing style, multilingual notes
- `docs/seo.md` — SEO strategy, Rank Math setup

---

## How We Work
- Davy provides screenshots + existing code snippets
- Claude writes PHP/CSS/HTML for Elementor HTML widgets or WordPress snippets
- No local dev environment — changes go directly into WordPress
- Always write mobile-first CSS
- Never break existing Elementor structure without flagging it first

---

## Important Rules
- Brand colors only (see `docs/brand.md`) — never use off-brand colors
- Font: Cormorant Garamond for headings, Inter for body — always
- All code must work inside Elementor HTML widget or WordPress Additional CSS
- Property data comes from ACF fields — always use `get_field()` to retrieve
- Responsive: Desktop → Tablet (768px) → Mobile (480px)
- The website tone is warm and premium, never pushy or generic
