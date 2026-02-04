<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Admin;

use WCBH\Domain\Brand\BrandTaxonomy;

final class ProductBrandFilter
{
    public function register(): void
    {
        add_action('restrict_manage_posts', [$this, 'render']);
    }

    public function render(): void
    {
        global $typenow;

        if ($typenow !== 'product') {
            return;
        }

        wp_dropdown_categories([
            'taxonomy'        => BrandTaxonomy::TAXONOMY,
            'name'            => BrandTaxonomy::TAXONOMY,
            'show_option_all' => __('All Brands', 'wcbh'),
            'hide_empty'      => false,
            'hierarchical'    => false,
            'value_field'     => 'slug',
            'selected'        => $_GET[BrandTaxonomy::TAXONOMY] ?? '',
        ]);
    }
}
