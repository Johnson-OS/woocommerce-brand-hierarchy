<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Query;

use WCBH\Domain\Brand\BrandTaxonomy;
use WP_Query;

final class BrandProductQuery
{
    public function register(): void
    {
        add_action('pre_get_posts', [$this, 'applyBrandFiltering']);
    }

    public function applyBrandFiltering(WP_Query $query): void
    {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }

        $brand = get_query_var('wcbh_brand');
        if (!$brand) {
            return;
        }

        $taxQuery = [
            'relation' => 'AND',
            [
                'taxonomy' => BrandTaxonomy::TAXONOMY,
                'field'    => 'slug',
                'terms'    => $brand,
            ],
        ];

        if ($subcat = get_query_var('wcbh_subcat')) {
            $taxQuery[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $subcat,
            ];
        } elseif ($cat = get_query_var('wcbh_cat')) {
            $taxQuery[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat,
            ];
        }

        $query->set('post_type', 'product');
        $query->set('tax_query', $taxQuery);
        $query->set('posts_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

        // Force Woo shop behavior
        $query->is_post_type_archive = true;
        $query->is_archive = true;
        $query->is_home = false;
        $query->is_shop = true;
    }

    public static function getProducts(
        int $brandId,
        ?int $categoryId = null,
        ?int $subCategoryId = null,
        int $perPage = 12,
        int $paged = 1
    ): \WP_Query {
        $taxQuery = [
            [
                'taxonomy' => 'product_brand',
                'terms' => $brandId,
            ],
        ];

        if ($subCategoryId) {
            $taxQuery[] = [
                'taxonomy' => 'product_cat',
                'terms' => $subCategoryId,
            ];
        }else if($categoryId){
            $taxQuery[] = [
                'taxonomy' => 'product_cat',
                'terms' => $categoryId,
            ];
        }

        return new \WP_Query([
            'post_type' => 'product',
            'posts_per_page' => $perPage,
            'paged' => $paged,
            'tax_query' => $taxQuery,
        ]);
    }
}
