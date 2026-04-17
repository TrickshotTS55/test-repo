<?php
/**
 * Pura Vida Real Estate — Single Property Template
 * Shortcode: [pura_property]
 *
 * SETUP INSTRUCTIONS:
 * 1. Delete old snippets: "Galerie 6 Bilder Limit", "Property Features Grid",
 *    "Property Description Format", "Preis Formatierung ACF Euro und Punkt"
 * 2. Create NEW snippet in Code Snippets → Add New (type: PHP)
 * 3. Paste this entire file content
 * 4. In Elementor Single Property template: delete all content,
 *    add one HTML widget, type [pura_property]
 * 5. Paste the CSS file into Appearance → Customize → Additional CSS
 *    (replace all old property-related CSS)
 */

function pura_single_property_shortcode() {

    $post_id = get_the_ID();

    // --- ACF Fields ---
    $price          = get_field('price', $post_id, false); // raw value, not filtered
    $bedrooms       = get_field('bedrooms', $post_id);
    $bathrooms      = get_field('bathrooms', $post_id);
    $size           = get_field('size', $post_id);
    $location       = get_field('location', $post_id);
    $features       = get_field('features', $post_id);
    $ref            = get_field('ref', $post_id);
    $description    = get_field('description', $post_id);
    $description_de = get_field('description_de', $post_id);
    $property_type  = get_field('property_type', $post_id);
    $province       = get_field('province', $post_id);
    $urbanisation   = get_field('urbanisation', $post_id);
    $pool           = get_field('pool', $post_id);
    $lat            = get_field('latitude', $post_id);
    $lng            = get_field('longitude', $post_id);

    // --- Price formatting: 315000 → € 315.000 ---
    // Strip ALL non-digit chars (including dots/commas from European-format stored values)
    $price_formatted = '';
    if ($price) {
        $clean_price = preg_replace('/[^0-9]/', '', (string)$price);
        if ($clean_price !== '') {
            $price_formatted = '€ ' . number_format((int)$clean_price, 0, ',', '.');
        }
    }

    // --- Gallery images ---
    $featured_id   = get_post_thumbnail_id($post_id);
    $main_img_url  = get_the_post_thumbnail_url($post_id, 'full');
    $all_media     = get_attached_media('image', $post_id);
    $all_media     = array_values($all_media);

    // Grid images: all attachments except the featured image, up to 5
    $grid_images = [];
    foreach ($all_media as $att) {
        if ($att->ID !== (int) $featured_id) {
            $grid_images[] = $att;
        }
        if (count($grid_images) >= 5) break;
    }
    // Fallback: if no featured image, use first attachment as main
    if (!$main_img_url && !empty($all_media)) {
        $main_img_url = wp_get_attachment_image_url($all_media[0]->ID, 'full');
        $grid_images  = array_slice($all_media, 1, 5);
    }

    // --- Description: handle &#13; entities from XML import ---
    $desc_paragraphs = [];
    if ($description) {
        $clean = $description;
        // WP All Import stores carriage returns as literal &#13; entities
        $clean = str_replace(['&#13;', '&#x0D;', '&#x0d;'], "\n", $clean);
        $clean = html_entity_decode($clean, ENT_QUOTES, 'UTF-8');
        $clean = preg_replace('/\r\n|\r/', "\n", $clean);
        $clean = preg_replace('/\n{3,}/', "\n\n", trim($clean));

        $paras = array_values(array_filter(
            array_map('trim', preg_split('/\n\s*\n/', $clean))
        ));

        // Fallback: if only 1 paragraph, split every 3 sentences
        if (count($paras) <= 1 && !empty($paras)) {
            $sentences = preg_split('/(?<=[.!?])\s+(?=[A-ZÜÖÄ\"\'])/', $paras[0]);
            $paras  = [];
            $buffer = '';
            $count  = 0;
            foreach ($sentences as $s) {
                $buffer .= ($buffer ? ' ' : '') . $s;
                $count++;
                if ($count >= 3) {
                    $paras[] = $buffer;
                    $buffer  = '';
                    $count   = 0;
                }
            }
            if ($buffer) $paras[] = $buffer;
        }

        $desc_paragraphs = array_values(array_filter($paras));
    }

    // --- Features array ---
    $features_array = [];
    if ($features) {
        $features_array = array_values(array_filter(
            array_map('trim', explode(',', $features))
        ));
    }

    ob_start();
    ?>

    <div class="pura-property-wrap">

        <!-- ==================== GALLERY HERO ==================== -->
        <div class="pura-gallery-hero">

            <div class="pura-gallery-main" onclick="puraOpenLightbox(0)">
                <?php if ($main_img_url): ?>
                    <img src="<?php echo esc_url($main_img_url); ?>"
                         alt="<?php echo esc_attr(get_the_title()); ?>">
                <?php endif; ?>
            </div>

            <div class="pura-gallery-grid">
                <?php foreach ($grid_images as $i => $img):
                    $img_url  = wp_get_attachment_image_url($img->ID, 'large');
                    $img_full = wp_get_attachment_image_url($img->ID, 'full');
                    $is_last  = ($i === count($grid_images) - 1) && count($grid_images) >= 5;
                ?>
                    <div class="pura-grid-item <?php echo $is_last ? 'pura-grid-last' : ''; ?>"
                         data-full="<?php echo esc_url($img_full); ?>">
                        <img src="<?php echo esc_url($img_url); ?>" alt="">
                        <?php if ($is_last): ?>
                            <div class="pura-see-all" onclick="puraOpenGallery()">SEE ALL PHOTOS</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

        <!-- ==================== INFO BAR ==================== -->
        <div class="pura-info-bar">

            <div class="pura-info-left">
                <h1 class="pura-title"><?php echo esc_html(get_the_title()); ?></h1>
                <a href="/contact" class="pura-cta-btn">ARRANGE A VIEWING</a>
                <p class="pura-cta-sub">Personal advice in English, German and Russian</p>
            </div>

            <div class="pura-info-right">
                <?php if ($price_formatted): ?>
                    <div class="pura-price"><?php echo esc_html($price_formatted); ?></div>
                <?php endif; ?>
                <?php if ($ref): ?>
                    <div class="pura-ref">Ref. <span><?php echo esc_html($ref); ?></span></div>
                <?php endif; ?>
                <div class="pura-specs">
                    <?php if ($bedrooms): ?>
                    <div class="pura-spec">
                        <span class="pura-spec-label">Bedrooms</span>
                        <span class="pura-spec-value"><?php echo esc_html($bedrooms); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($bathrooms): ?>
                    <div class="pura-spec">
                        <span class="pura-spec-label">Bathrooms</span>
                        <span class="pura-spec-value"><?php echo esc_html($bathrooms); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($size): ?>
                    <div class="pura-spec">
                        <span class="pura-spec-label">m²</span>
                        <span class="pura-spec-value"><?php echo esc_html($size); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ==================== TABS ==================== -->
        <div class="pura-tabs-wrap">

            <div class="pura-tabs-nav" role="tablist">
                <button class="pura-tab active" data-tab="description" role="tab">DESCRIPTION</button>
                <button class="pura-tab" data-tab="overview" role="tab">OVERVIEW</button>
                <button class="pura-tab" data-tab="features" role="tab">FEATURES &amp; AMENITIES</button>
            </div>

            <!-- DESCRIPTION -->
            <div class="pura-tab-content active" id="pura-tab-description">
                <div class="pura-two-col">

                    <div class="pura-description-col">
                        <?php if (!empty($desc_paragraphs)): ?>
                        <div class="pura-description-wrap" id="pura-desc-wrap">
                            <div class="pura-description">
                                <?php foreach (array_slice($desc_paragraphs, 0, 2) as $i => $para): ?>
                                    <p class="<?php echo $i === 0 ? 'pura-desc-intro' : ''; ?>">
                                        <?php echo esc_html($para); ?>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($desc_paragraphs) > 2): ?>
                                <div class="pura-desc-reveal" id="pura-desc-reveal">
                                    <div class="pura-desc-reveal-inner">
                                        <?php foreach (array_slice($desc_paragraphs, 2) as $para): ?>
                                            <p><?php echo esc_html($para); ?></p>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="pura-desc-fade" id="pura-desc-fade"></div>
                                <div class="pura-read-more-wrap">
                                    <button class="pura-read-more" id="pura-read-more-btn" onclick="puraToggleDesc()">
                                        <span class="pura-rm-line"></span>
                                        <span class="pura-rm-text">READ MORE</span>
                                        <span class="pura-rm-line"></span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="pura-features-col">
                        <?php if (!empty($features_array)): ?>
                        <div class="pura-features-sidebar">
                            <h3 class="pura-features-title">FEATURES &amp; AMENITIES</h3>
                            <div class="pura-features-grid">
                                <?php foreach ($features_array as $feature): ?>
                                    <div class="pura-feature-item">
                                        <span class="pura-feature-check">✓</span>
                                        <span class="pura-feature-text">
                                            <?php echo esc_html(strtoupper($feature)); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- OVERVIEW -->
            <div class="pura-tab-content" id="pura-tab-overview">
                <div class="pura-overview-grid">
                    <?php if ($property_type): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">TYPE</span>
                        <span class="pura-ov-value"><?php echo esc_html($property_type); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($location): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">TOWN</span>
                        <span class="pura-ov-value"><?php echo esc_html($location); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($urbanisation): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">URBANISATION</span>
                        <span class="pura-ov-value"><?php echo esc_html($urbanisation); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($province): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">REGION</span>
                        <span class="pura-ov-value"><?php echo esc_html($province); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($size): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">BUILT AREA</span>
                        <span class="pura-ov-value"><?php echo esc_html($size); ?> m²</span>
                    </div>
                    <?php endif; ?>
                    <?php if ($bedrooms): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">BEDROOMS</span>
                        <span class="pura-ov-value"><?php echo esc_html($bedrooms); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($bathrooms): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">BATHROOMS</span>
                        <span class="pura-ov-value"><?php echo esc_html($bathrooms); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">POOL</span>
                        <span class="pura-ov-value"><?php echo $pool ? 'Yes' : 'No'; ?></span>
                    </div>
                    <?php if ($ref): ?>
                    <div class="pura-ov-item">
                        <span class="pura-ov-label">REFERENCE</span>
                        <span class="pura-ov-value"><?php echo esc_html($ref); ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($lat && $lng): ?>
                <div class="pura-map-wrap">
                    <iframe
                        src="https://maps.google.com/maps?q=<?php echo esc_attr($lat . ',' . $lng); ?>&z=14&output=embed"
                        loading="lazy" allowfullscreen title="Property location map">
                    </iframe>
                </div>
                <?php endif; ?>
            </div>

            <!-- FEATURES TAB (standalone) -->
            <div class="pura-tab-content" id="pura-tab-features">
                <?php if (!empty($features_array)): ?>
                <div class="pura-features-full">
                    <?php foreach ($features_array as $feature): ?>
                        <div class="pura-feature-item">
                            <span class="pura-feature-check">✓</span>
                            <span class="pura-feature-text">
                                <?php echo esc_html(strtoupper($feature)); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
        <!-- end tabs -->

    </div>
    <!-- end pura-property-wrap -->

    <!-- ==================== FULL GALLERY LIGHTBOX ==================== -->
    <div class="pura-lightbox" id="pura-lightbox" onclick="puraCloseLightbox(event)">
        <button class="pura-lb-close" onclick="puraCloseLightbox()">&times;</button>
        <button class="pura-lb-prev" onclick="puraLbNav(-1)">&#8249;</button>
        <button class="pura-lb-next" onclick="puraLbNav(1)">&#8250;</button>
        <div class="pura-lb-img-wrap">
            <img id="pura-lb-img" src="" alt="">
        </div>
    </div>

    <script>
    (function() {

        // --- Tab switching ---
        document.querySelectorAll('.pura-tab').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.pura-tab').forEach(function(b) {
                    b.classList.remove('active');
                });
                document.querySelectorAll('.pura-tab-content').forEach(function(c) {
                    c.classList.remove('active');
                });
                this.classList.add('active');
                var target = document.getElementById('pura-tab-' + this.dataset.tab);
                if (target) target.classList.add('active');
            });
        });

        // --- Read more: elegant gradient-reveal ---
        window.puraToggleDesc = function() {
            var reveal = document.getElementById('pura-desc-reveal');
            var fade   = document.getElementById('pura-desc-fade');
            var btn    = document.getElementById('pura-read-more-btn');
            var wrap   = document.getElementById('pura-desc-wrap');
            if (!reveal) return;

            var isOpen = reveal.classList.contains('open');

            if (!isOpen) {
                // Reveal: first fade out the gradient, then expand
                if (fade) fade.classList.add('hiding');
                setTimeout(function() {
                    reveal.classList.add('open');
                    if (btn) btn.classList.add('open');
                    if (wrap) wrap.classList.add('expanded');
                    setTimeout(function() {
                        if (fade) fade.style.display = 'none';
                        if (btn) {
                            btn.querySelector('.pura-rm-text').textContent = 'READ LESS';
                        }
                    }, 200);
                }, 200);
            } else {
                // Collapse
                reveal.classList.remove('open');
                if (btn) btn.classList.remove('open');
                if (wrap) wrap.classList.remove('expanded');
                if (fade) {
                    fade.style.display = '';
                    setTimeout(function() { fade.classList.remove('hiding'); }, 20);
                }
                if (btn) btn.querySelector('.pura-rm-text').textContent = 'READ MORE';
                window.scrollTo({ top: reveal.getBoundingClientRect().top + window.scrollY - 200, behavior: 'smooth' });
            }
        };

        // --- Lightbox ---
        var lbImages = [];
        var lbIndex  = 0;

        // Collect all gallery images for lightbox
        document.querySelectorAll('.pura-gallery-main img, .pura-grid-item img').forEach(function(img) {
            lbImages.push(img.src);
        });

        // Click main image to open lightbox
        var mainImg = document.querySelector('.pura-gallery-main img');
        if (mainImg) {
            mainImg.style.cursor = 'pointer';
            mainImg.addEventListener('click', function() { puraOpenLightbox(0); });
        }

        // Click grid images (not the last "SEE ALL PHOTOS" one — that opens all)
        document.querySelectorAll('.pura-grid-item:not(.pura-grid-last) img').forEach(function(img, i) {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function() { puraOpenLightbox(i + 1); });
        });

        window.puraOpenGallery = function() { puraOpenLightbox(0); };

        window.puraOpenLightbox = function(index) {
            lbIndex = index || 0;
            document.getElementById('pura-lb-img').src = lbImages[lbIndex];
            document.getElementById('pura-lightbox').classList.add('open');
            document.body.style.overflow = 'hidden';
        };

        window.puraCloseLightbox = function(e) {
            if (e && e.target !== e.currentTarget && !e.target.classList.contains('pura-lb-close')) return;
            document.getElementById('pura-lightbox').classList.remove('open');
            document.body.style.overflow = '';
        };

        window.puraLbNav = function(dir) {
            lbIndex = (lbIndex + dir + lbImages.length) % lbImages.length;
            document.getElementById('pura-lb-img').src = lbImages[lbIndex];
        };

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            var lb = document.getElementById('pura-lightbox');
            if (!lb.classList.contains('open')) return;
            if (e.key === 'ArrowRight') puraLbNav(1);
            if (e.key === 'ArrowLeft')  puraLbNav(-1);
            if (e.key === 'Escape')     puraCloseLightbox();
        });

    })();
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('pura_property', 'pura_single_property_shortcode');

// Add body class on property pages so nav CSS targeting is reliable
add_filter('body_class', function($classes) {
    if (is_singular('property_item') || is_post_type_archive('property_item')) {
        $classes[] = 'pura-property-page';
    }
    return $classes;
});
