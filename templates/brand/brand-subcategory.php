<?php

use WCBH\Domain\Brand\BrandTaxonomy;
use WCBH\Infrastructure\Query\BrandProductQuery;
defined('ABSPATH') || exit;

$brandSlug = get_query_var('wcbh_brand');
$catSlug   = get_query_var('wcbh_cat');
$subSlug   = get_query_var('wcbh_subcat');

$brand = get_term_by('slug', $brandSlug, BrandTaxonomy::TAXONOMY);
$cat   = get_term_by('slug', $catSlug, 'product_cat');
$sub   = get_term_by('slug', $subSlug, 'product_cat');

if (!$brand || !$cat || !$sub) {
    wp_safe_redirect(home_url());
    exit;
}
$paged = max(1, get_query_var('paged'));

$products = BrandProductQuery::getProducts(
    $brand->term_id,
    $cat->term_id,
    $sub->term_id,
    12,
    $paged
);
get_header();
?>

<div class="wcbh-page wcbh-brand-subcategory-page">

    <div class="wcbh-top-header">

        <div style="width: 100%; display: flex; flex-direction: column; align-items: start">
            <?php woocommerce_breadcrumb(); ?>
            <header class="wcbh-brand-header">
                <h6 class="page-title"><?= esc_html($sub->name); ?></h6>
            </header>
        </div>

        <?php wcbh_render_brand_thumbnail($brand->term_id)?>

    </div>

    <div class="wcbh-brand-banner-row">
        <!-- Products grid -->
        <div class="wcbh-products-grid">

            <?php
            global $wp_query;
            $original_query = $wp_query;
            $wp_query = $products;

            if (have_posts()) :

                woocommerce_product_loop_start();

                while (have_posts()) :
                    the_post();
                    wc_get_template_part('content', 'product');
                endwhile;

                woocommerce_product_loop_end();

                echo paginate_links([
                    'total'   => $products->max_num_pages,
                    'current' => $paged,
                ]);

            else : ?>

                <p class="woocommerce-info">
                    <?php esc_html_e('No products found.', 'woocommerce'); ?>
                </p>

            <?php endif;

            $wp_query = $original_query;
            wp_reset_postdata();
            ?>

        </div>

        <?php
        if (function_exists('wcbh_render_brand_banner')) {
            wcbh_render_brand_banner($brand->term_id);
        }
        ?>

    </div>


</div>

<?php get_footer(); ?>
