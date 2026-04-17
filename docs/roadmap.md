# Project Roadmap – Pura Vida Real Estate

## Build Sequence

Work through these phases in order. Each phase is a prerequisite for the next.

---

## Phase 1 — Foundation (ACF + XML Import)
**Status: COMPLETE**

Goal: Get all property data from the XML feed correctly stored in WordPress.

### Steps:
- [x] ACF Field Group "Property Details" created with basic fields
- [x] Added missing ACF fields: `property_type`, `province`, `urbanisation`, `pool`, `latitude`, `longitude`, `description_de`
- [x] Updated WP All Import mapping for all new fields
- [x] Re-ran import — all key fields populated correctly
- [x] Fixed `ref` field type: Number → Text, re-imported, IS-89171 now correct
- [x] `distance_to_beach` / `distance_to_airport` — intentionally left empty, info comes from `features` field instead
- [ ] Set up live XML feed URL for automatic sync (later)
- [ ] Set up live XML feed URL (replace manual upload)

---

## Phase 2 — Single Property Template
**Status: IN PROGRESS**

Goal: A premium, fully responsive single property page.

### Sections to build:
- [ ] Hero photo gallery (main image + 2×3 grid with "SEE ALL PHOTOS")
- [ ] Property title (H1, auto from post title)
- [ ] Info bar: price, ref, bedrooms, bathrooms, m²
- [ ] "ARRANGE A VIEWING" CTA button
- [ ] Tab navigation: Description | Overview | Features & Amenities | Downloads
- [ ] Description tab content (EN + DE via TranslatePress)
- [ ] Features & Amenities (checkmark grid from `features` ACF field)
- [ ] Overview tab: distances, pool, type, urbanisation, GPS map
- [ ] SEO: auto-generated title tag, meta, schema markup
- [ ] Responsive: desktop → tablet → mobile

---

## Phase 3 — Properties Archive Template
**Status: NOT STARTED**

Goal: A filterable property listing page showing all 1303 properties.

### To build:
- [ ] Property card component (photo, title, price, beds/baths/m², location)
- [ ] Grid layout (3 columns desktop, 2 tablet, 1 mobile)
- [ ] Filter sidebar or top bar (type, location, price range, beds)
- [ ] Pagination or infinite scroll
- [ ] SEO: archive page title and meta

---

## Phase 4 — TranslatePress Setup
**Status: NOT STARTED**

Goal: English as primary, German and Russian translations active.

### Steps:
- [ ] Configure TranslatePress language settings (EN primary, DE + RU)
- [ ] Set up language switcher in navigation
- [ ] Translate key pages manually (Home, About, Buying Process, Contact)
- [ ] Use XML's `desc/de` field for German property descriptions (via `description_de` ACF field)
- [ ] Test language switching on property pages

---

## Phase 5 — Area Guide Pages
**Status: NOT STARTED**

Goal: SEO-rich content pages for each major location.

### Priority areas:
Costa Blanca South: Orihuela Costa, Torrevieja, Pilar de la Horadada, Villamartín, La Zenia, Cabo Roig, Ciudad Quesada, Guardamar
Costa Cálida: Los Alcázares, San Pedro del Pinatar, La Manga del Mar Menor

### Each page includes:
- [ ] Hero image/video of the area
- [ ] Lifestyle description
- [ ] Key info: beaches, amenities, distances, climate
- [ ] "Properties in [Area]" filtered link
- [ ] SEO optimized (see `docs/seo.md`)

---

## Phase 6 — Video Integration
**Status: NOT STARTED**

Goal: Video as a core trust-building element throughout the site.

### Planned:
- [ ] Homepage hero video background (YouTube embed or self-hosted)
- [ ] Area guide videos (YouTube embeds)
- [ ] Property video tours (optional per listing)
- [ ] "Meet Davy & Marina" video on About page
- [ ] VideoObject schema markup on video pages

---

## Phase 7 — Performance & SEO Polish
**Status: NOT STARTED**

- [ ] Core Web Vitals check (Google Search Console)
- [ ] Image optimization (WebP via Image Optimizer plugin)
- [ ] Rank Math: focus keywords per property, schema per page type
- [ ] Google Search Console verification (Site Kit active)
- [ ] Sitemap submitted to Google
- [ ] robots.txt check

---

## Notes
- After each phase: commit notes to this roadmap file
- Always test on mobile before marking a step done
- TranslatePress: configure only after templates are finalized (to avoid re-translating layouts)
