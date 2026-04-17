# Property Import – XML Feed & ACF Setup

## Overview
Properties are imported from an XML feed provided by **InSun Properties** using **WP All Import Pro**. All field data is stored in **Advanced Custom Fields (ACF)** and displayed via Elementor templates using PHP snippets.

**Feed size:** 1303 properties  
**Status:** Manual test import done (3 properties). Live URL feed — next step.

---

## ACF Field Group: "Property Details"

These are the exact field names as configured in WordPress ACF:

| # | Label              | ACF Field Name       | Type   | Notes                                      |
|---|--------------------|----------------------|--------|--------------------------------------------|
| 1 | Price              | `price`              | Number | Raw number, format on output               |
| 2 | Bedrooms           | `bedrooms`           | Number |                                            |
| 3 | Bathrooms          | `bathrooms`          | Number |                                            |
| 4 | Size               | `size`               | Text   | Built area in m²                           |
| 5 | Location           | `location`           | Text   | Town name (e.g. "Villamartin")             |
| 6 | Distance to beach  | `distance_to_beach`  | Number | Mapped from feature[8] in XML              |
| 7 | Distance to airport| `distance_to_airport`| Number | Mapped from feature[9] in XML              |
| 8 | Features           | `features`           | Text   | All features as text (comma/newline sep.)  |
| 9 | Ref.               | `ref`                | Text   | Property reference (e.g. IS-89171) — must be Text, not Number |
|10 | Description        | `description`        | Text   | English description (plain text)           |

---

## WP All Import Pro – Field Mapping

| ACF Field          | XML XPath mapping                          |
|--------------------|--------------------------------------------|
| price              | `{price[1]}`                               |
| bedrooms           | `{beds[1]}`                                |
| bathrooms          | `{baths[1]}`                               |
| size               | `{surface_area[1]/built[1]}`               |
| location           | `{town[1]}`                                |
| distance_to_beach  | `{features[1]/feature[8]}`                 |
| distance_to_airport| `{features[1]/feature[9]}`                 |
| features           | `{features[1]/feature}`                    |
| ref                | `{ref[1]}`                                 |
| description        | `{desc[1]/en[1]}`                          |

---

## Full XML Feed Structure (InSun Properties)

```xml
<property>
  <id>70</id>
  <date>2026-03-04 16:15:48</date>
  <ref>IS-89171</ref>
  <price>315000</price>
  <currency>EUR</currency>
  <price_freq>sale</price_freq>
  <type>Apartment</type>                    <!-- NOT yet in ACF -->
  <town>Villamartin</town>
  <province>Costa Blanca South</province>  <!-- NOT yet in ACF -->
  <location_detail>Los Dolses</location_detail> <!-- NOT yet in ACF — urbanisation -->
  <beds>2</beds>
  <baths>2</baths>
  <pool>1</pool>                            <!-- NOT yet in ACF -->
  <location>
    <latitude>37.935400</latitude>          <!-- NOT yet in ACF -->
    <longitude>-0.744418</longitude>        <!-- NOT yet in ACF -->
  </location>
  <energy_rating>
    <consumption>X</consumption>            <!-- NOT yet in ACF -->
    <emissions>X</emissions>               <!-- NOT yet in ACF -->
  </energy_rating>
  <surface_area>
    <built>82</built>
  </surface_area>
  <url>
    <en>https://insunproperties.com/property/70/...</en> <!-- NOT yet in ACF -->
  </url>
  <desc>
    <de>German description...</de>         <!-- NOT yet in ACF -->
    <en>English description...</en>
    <fr>French description...</fr>         <!-- NOT yet in ACF -->
    <sv>Swedish description...</sv>        <!-- NOT yet in ACF -->
  </desc>
  <features>
    <feature>Panoramic views</feature>
    <feature>Gated complex</feature>
    <feature>Covered terrace</feature>
    <feature>Terrace</feature>
    <feature>Communal pool</feature>
    <feature>Landscaped gardens</feature>
    <feature>Stone walls</feature>
    <feature>Near beach</feature>           <!-- feature[8] → distance_to_beach -->
    <feature>Close to airport</feature>     <!-- feature[9] → distance_to_airport -->
    <feature>Near Golf / Golf Resort Property</feature>
    <feature>Near Schools</feature>
    <feature>Near Commercial Center</feature>
    <feature>Near Bus Route</feature>
    <feature>Popular urbanisation</feature>
    <feature>Tile floors</feature>
    <feature>Built-in wardrobes</feature>
    <feature>Alarm</feature>
    <feature>Reinforced door</feature>
    <feature>Double glazed windows</feature>
    <feature>Storage room</feature>
    <feature>Internet</feature>
    <feature>Garage</feature>
    <feature>Electric gate</feature>
    <feature>Parking</feature>
    <feature>Air conditioning</feature>
    <feature>Coastal, Urbanisation</feature>
    <feature>Lift</feature>
    <feature>Stone floors</feature>
    <feature>Central electric heating</feature>
    <feature>Doorbell with camera</feature>
  </features>
  <images>
    <image id="1">
      <url>https://insunproperties.com/media/images/properties/...</url>
    </image>
    <!-- up to 30+ images per property -->
  </images>
</property>
```

---

## ACF Fields To Add (Future)

These XML fields exist but are not yet mapped. Add them in ACF + WP All Import:

| XML Field            | Suggested ACF Name   | Type   | Priority |
|----------------------|----------------------|--------|----------|
| `type`               | `property_type`      | Text   | HIGH — needed for filtering and display |
| `province`           | `province`           | Text   | HIGH — for SEO and archive filtering |
| `location_detail`    | `urbanisation`       | Text   | HIGH — important for buyer searches |
| `pool`               | `pool`               | Number | MEDIUM — 1 = yes, 0 = no |
| `latitude`           | `latitude`           | Text   | MEDIUM — for Google Maps |
| `longitude`          | `longitude`          | Text   | MEDIUM — for Google Maps |
| `desc/de`            | `description_de`     | Text   | MEDIUM — German market |
| `energy_rating`      | `energy_rating`      | Text   | LOW |
| `url/en`             | `source_url`         | URL    | LOW |

---

## PHP — Retrieving ACF Fields

```php
// Current active fields
$price       = get_field('price');
$bedrooms    = get_field('bedrooms');
$bathrooms   = get_field('bathrooms');
$size        = get_field('size');
$location    = get_field('location');
$beach       = get_field('distance_to_beach');
$airport     = get_field('distance_to_airport');
$features    = get_field('features');
$ref         = get_field('ref');
$description = get_field('description');

// Format price with European notation: 315000 → € 315.000
$price_formatted = '€ ' . number_format($price, 0, ',', '.');

// Features are stored as text — split into array for display
$features_array = array_filter(array_map('trim', explode(',', $features)));
// Or if newline-separated:
$features_array = array_filter(array_map('trim', explode("\n", $features)));
```

---

## Images
Images are stored in the WordPress gallery (imported via WP All Import image handling). Retrieve via:
```php
$gallery = get_post_gallery_images(get_the_ID());
// or via ACF gallery field if set up
$gallery = get_field('gallery');
```
