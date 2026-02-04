<?php
defined('ABSPATH') || exit;

use WCBH\Domain\Brand\BrandTaxonomy;
use WCBH\Presentation\BrandNavigation;

$brandSlug = get_query_var('wcbh_brand');
$catSlug   = get_query_var('wcbh_cat');

$brand = get_term_by('slug', $brandSlug, BrandTaxonomy::TAXONOMY);
$cat   = get_term_by('slug', $catSlug, 'product_cat');

$subcategories = BrandNavigation::subcategoriesForBrand($brandSlug, (int) $cat->term_id);

get_header();
?>

<div class="wcbh-page">

    <div class="wcbh-top-header">

        <div style="display: flex; flex-direction: column; align-items: start">
            <?php woocommerce_breadcrumb(); ?>
            <header class="wcbh-brand-header">
                <h6 class="page-title"><?= esc_html($cat->name); ?></h6>
            </header>
        </div>

        <?php wcbh_render_brand_thumbnail($brand->term_id)?>

    </div>


    <div class="wcbh-brand-banner-row">
        <!-- Subcategories grid -->
        <?php if (!empty($subcategories)) : ?>
            <div class="brand-categories-grid">
                <?php foreach ($subcategories as $sub) :

                    $image = $sub->image_url ?: wc_placeholder_img_src();
                    $count = $sub->product_count;

                    ?>
                    <div class="brand-category-card">
                        <a href="<?= esc_url(
                            home_url("/brand/{$brand->slug}/{$cat->slug}/{$sub->slug}/")
                        ); ?>">

                            <?php
                            if ($image) {
                                echo "<img class=\"brand-category-image\" src=\"" . esc_url($image) . "\" alt=\"" . esc_attr($cat->name) . "\">";
                            } else {
                                echo wc_placeholder_img('medium');
                            }
                            ?>

                            <h6 class="brand-category-title">
                                <?= esc_html($sub->name); ?>
                            </h6>

                            <?php if (isset($sub->product_count)) : ?>
                               <!-- <span class="brand-category-count">
                                <?php /*= (int) $sub->product_count; */?> products
                            </span>-->
                            <?php endif; ?>

                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="woocommerce-info">
                <?php esc_html_e('No subcategories found for this category.', 'woocommerce'); ?>
            </p>
        <?php endif; ?>

        <?php
        if (function_exists('wcbh_render_brand_banner')) {
            wcbh_render_brand_banner($brand->term_id);
        }
        ?>
    </div>

</div>

<?php get_footer(); ?>
