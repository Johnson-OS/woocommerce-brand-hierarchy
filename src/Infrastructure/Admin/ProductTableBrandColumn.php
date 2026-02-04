<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Admin;

use WCBH\Domain\Brand\BrandTaxonomy;

final class ProductTableBrandColumn
{
    public function register(): void
    {
        add_filter('manage_edit-product_columns', [$this, 'addColumn']);
        add_action('manage_product_posts_custom_column', [$this, 'renderColumn'], 10, 2);
    }

    public function addColumn(array $columns): array
    {
        $columns['wcbh_brand'] = __('Brand', 'wcbh');
        return $columns;
    }

    public function renderColumn(string $column, int $postId): void
    {
        if ($column !== 'wcbh_brand') {
            return;
        }

        $terms = get_the_terms($postId, BrandTaxonomy::TAXONOMY);

        if (empty($terms) || is_wp_error($terms)) {
            echo '—';
            return;
        }

        $links = array_map(function ($term) {
            $url = admin_url(
                'edit.php?post_type=product&' .
                BrandTaxonomy::TAXONOMY . '=' . $term->slug
            );

            return sprintf(
                '<a href="%s">%s</a>',
                esc_url($url),
                esc_html($term->name)
            );
        }, $terms);

        echo implode(', ', $links);
    }
}
