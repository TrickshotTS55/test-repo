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

    // All non-featured images (1 shown in grid, rest available in lightbox)
    $all_non_featured = [];
    foreach ($all_media as $att) {
        if ($att->ID !== (int) $featured_id) {
            $all_non_featured[] = $att;
        }
    }
    $grid_images   = array_slice($all_non_featured, 0, 1);
    $see_all_thumb = isset($all_non_featured[1]) ? $all_non_featured[1]
                   : (!empty($grid_images) ? $grid_images[0] : null);

    // Fallback: if no featured image, use first attachment as main
    if (!$main_img_url && !empty($all_media)) {
        $main_img_url     = wp_get_attachment_image_url($all_media[0]->ID, 'full');
        $all_non_featured = array_slice($all_media, 1);
        $grid_images      = array_slice($all_non_featured, 0, 1);
        $see_all_thumb    = isset($all_non_featured[1]) ? $all_non_featured[1]
                          : (!empty($grid_images) ? $grid_images[0] : null);
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
                ?>
                    <div class="pura-grid-item" data-full="<?php echo esc_url($img_full); ?>"
                         data-lb-index="<?php echo $i + 1; ?>">
                        <img src="<?php echo esc_url($img_url); ?>" alt="">
                    </div>
                <?php endforeach; ?>
                <!-- 4th cell: always SEE ALL PHOTOS -->
                <div class="pura-grid-item pura-grid-last" onclick="puraOpenGallery()">
                    <?php if ($see_all_thumb): ?>
                        <img src="<?php echo esc_url(wp_get_attachment_image_url($see_all_thumb->ID, 'large')); ?>" alt="">
                    <?php endif; ?>
                    <div class="pura-see-all">SEE ALL PHOTOS</div>
                </div>
            </div>

        </div>

        <!-- Hidden image list: all images for lightbox (including those not shown in grid) -->
        <?php
        $lb_urls = $main_img_url ? [$main_img_url] : [];
        foreach ($all_non_featured as $att) {
            $u = wp_get_attachment_image_url($att->ID, 'full');
            if ($u) $lb_urls[] = $u;
        }
        ?>
        <div id="pura-all-images" style="display:none">
            <?php foreach ($lb_urls as $lb_url): ?>
                <span data-url="<?php echo esc_url($lb_url); ?>"></span>
            <?php endforeach; ?>
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
                        <span class="pura-spec-icon"><svg width="20" height="13" viewBox="0 0 20 13" fill="none"><rect x="1" y="1" width="18" height="7" rx="1" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.3"/><rect x="2.5" y="2.5" width="5" height="4" rx="0.5" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.1"/><rect x="12.5" y="2.5" width="5" height="4" rx="0.5" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.1"/><path d="M1 8v4M19 8v4" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.3" stroke-linecap="round"/></svg></span>
                        <span class="pura-spec-label">BEDROOMS</span>
                        <span class="pura-spec-value"><?php echo esc_html($bedrooms); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($bathrooms): ?>
                    <div class="pura-spec">
                        <span class="pura-spec-icon"><svg width="18" height="15" viewBox="0 0 18 15" fill="none"><path d="M3 1v6" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.3" stroke-linecap="round"/><circle cx="3" cy="3" r="1.5" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.1"/><rect x="1" y="7" width="16" height="3" rx="1" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.3" fill="none"/><path d="M4 10v3M14 10v3" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.3" stroke-linecap="round"/></svg></span>
                        <span class="pura-spec-label">BATHROOMS</span>
                        <span class="pura-spec-value"><?php echo esc_html($bathrooms); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($size): ?>
                    <div class="pura-spec">
                        <span class="pura-spec-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M1 5V1h4M11 1h4v4M15 11v4h-4M5 15H1v-4" stroke="#C9A84C" stroke-opacity="1" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <span class="pura-spec-label">m²</span>
                        <span class="pura-spec-value"><?php echo esc_html($size); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- ==================== CONTENT ==================== -->
        <div class="pura-content-section">
            <div class="pura-two-col">

                <div class="pura-description-col">
                    <?php if (!empty($desc_paragraphs)): ?>
                    <div class="pura-description-wrap" id="pura-desc-wrap">
                        <h3 class="pura-col-heading">Description</h3>
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
                        <h3 class="pura-features-title">Features &amp; Amenities</h3>
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

        // Use hidden data list (includes all attached images, not just the displayed ones)
        var allImgList = document.getElementById('pura-all-images');
        if (allImgList) {
            allImgList.querySelectorAll('[data-url]').forEach(function(el) {
                if (el.dataset.url) lbImages.push(el.dataset.url);
            });
        }
        if (!lbImages.length) {
            document.querySelectorAll('.pura-gallery-main img, .pura-grid-item:not(.pura-grid-last) img').forEach(function(img) {
                if (img.src) lbImages.push(img.src);
            });
        }

        // Click grid items using data-lb-index (main image uses onclick attr directly)
        document.querySelectorAll('.pura-grid-item:not(.pura-grid-last)').forEach(function(item) {
            var idx = parseInt(item.dataset.lbIndex || 1);
            item.addEventListener('click', function() { puraOpenLightbox(idx); });
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

        // Inject centered logo into Elementor header (property pages only, no-op on homepage)
        (function() {
            var header = document.querySelector('header[data-elementor-type="header"]');
            if (!header || header.querySelector('.pura-header-logo')) return;
            header.style.position = 'relative';
            var logo = document.createElement('a');
            logo.href = '/';
            logo.className = 'pura-header-logo';
            logo.innerHTML = '<img src="https://puravida-realestate.es/wp-content/uploads/2026/03/Pura-Vida-Real-Estate-Logo-FINAL-Blue-v3.avif" alt="Pura Vida Real Estate">';
            header.appendChild(logo);
        })();

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
