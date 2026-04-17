# Property Import – XML Feed & ACF Setup

## Overview
Properties are imported from an XML feed provided by InSun Properties using **WP All Import Pro**. All field data is stored in **Advanced Custom Fields (ACF)** and displayed via Elementor templates using PHP snippets or ACF dynamic tags.

## Current Status
- 3 test properties imported successfully (manual XML upload)
- Next phase: live XML feed via URL (automatic sync)
- ACF fields exist but may need expansion — XML feed contains more data than currently mapped

---

## ACF Fields (Currently Active)

| Field Name    | ACF Key (example)    | Type   | Example Value        |
|---------------|----------------------|--------|----------------------|
| `price`       | `field_price`        | Number | `875000`             |
| `bedrooms`    | `field_bedrooms`     | Number | `3`                  |
| `bathrooms`   | `field_bathrooms`    | Number | `3`                  |
| `size`        | `field_size`         | Number | `232` (m²)           |
| `location`    | `field_location`     | Text   | `Capdepera`          |
| `description` | `field_description`  | Textarea | Long text          |
| `features`    | `field_features`     | Textarea / Repeater | Comma-separated or repeater |

> **Note:** Update this table as new fields are added from the XML feed.

---

## XML Feed Fields (Known / To Be Mapped)
The InSun Properties XML likely contains additional fields including:
- Property reference number (Ref)
- Property type (Villa, Apartment, Townhouse, etc.)
- Region / Area
- Sub-area / Urbanisation
- Plot size
- Year built
- Energy certificate
- Multiple photos (gallery)
- Floor plan PDF
- Video URL
- GPS coordinates
- Status (Available, Reserved, Sold)
- Property category (Resale, New Build)

> **Action needed:** Compare full XML structure with current ACF fields and map missing fields in WP All Import Pro.

---

## How to Retrieve ACF Fields in PHP

```php
// Basic field retrieval
$price      = get_field('price');
$bedrooms   = get_field('bedrooms');
$bathrooms  = get_field('bathrooms');
$size       = get_field('size');
$location   = get_field('location');
$description = get_field('description');
$features   = get_field('features');

// Format price with European notation
$price_formatted = number_format($price, 0, ',', '.'); // → 875.000
```

---

## WP All Import Pro Setup Notes
- Import type: Custom Post Type (likely `property` or similar)
- Field mapping: XML node → ACF field key
- Unique identifier: Use the property reference number to prevent duplicates on re-import
- Image handling: Map gallery images to ACF gallery field or WP featured image
- Schedule: Manual for now → set up cron/auto-import once feed URL is confirmed

---

## Elementor Template Notes
- Single Property Template: Applied to `property` post type
- Archive Template: Applied to `property` archive
- Use ACF dynamic tags in Elementor where possible (no code needed)
- Use HTML widget + PHP snippet for complex layouts (gallery grid, features list, tabs)
- PHP in Elementor HTML widgets requires a snippet plugin (e.g. WPCode or Code Snippets)
