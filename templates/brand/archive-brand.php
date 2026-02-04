<?php
defined('ABSPATH') || exit;

use WCBH\Domain\Brand\BrandTaxonomy;
use WCBH\Presentation\BrandNavigation;

$brandSlug = get_query_var('wcbh_brand');
$brand     = get_term_by('slug', $brandSlug, BrandTaxonomy::TAXONOMY);

$categories = BrandNavigation::categoriesForBrand($brandSlug);

get_header();
?>

<div class="wcbh-page">
    <div class="wcbh-top-header">

        <div style="display: flex; flex-direction: column; align-items: start">
            <?php woocommerce_breadcrumb(); ?>
            <header class="wcbh-brand-header">
                <h6 class="page-title"><?= esc_html($brand->name); ?></h6>
            </header>
        </div>

        <?php wcbh_render_brand_thumbnail($brand->term_id)?>

    </div>


    <div class="wcbh-brand-banner-row">
        <!-- Brand categories grid -->
        <?php if (!empty($categories)) : ?>
            <div class="brand-categories-grid">
                <?php foreach ($categories as $cat) :
                    $image = $cat->image_url ?: wc_placeholder_img_src();
                    $count = $cat->product_count;
                    ?>
                    <div class="brand-category-card">
                        <a href="<?= esc_url(home_url("/brand/{$brand->slug}/{$cat->slug}/")) ?>">
                            <?php
                            if ($image) {

                                echo "<img class=\"brand-category-image\" src=\"" . esc_url($image) . "\" alt=\"" . esc_attr($cat->name) . "\">";
                            } else {
                                echo wc_placeholder_img('medium');
                            }
                            ?>
                            <h6 class="brand-category-title">
                                <?= esc_html($cat->name); ?>
                            </h6>

                            <?php if (isset($cat->product_count)) : ?>
                                <!--<span class="brand-category-count">
                                <?php /*= (int) $cat->product_count; */?> products
                            </span>-->
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="woocommerce-info">
                <?php esc_html_e('No categories found for this brand.', 'woocommerce'); ?>
            </p>
        <?php endif; ?>

        <?php
        if (function_exists('wcbh_render_brand_banner')) {
            wcbh_render_brand_banner($brand->term_id);
        }
        ?>
    </div>


</div>

<?php

get_footer();
?>
