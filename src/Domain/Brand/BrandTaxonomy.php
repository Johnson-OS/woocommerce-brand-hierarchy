<?php

declare(strict_types=1);

namespace WCBH\Domain\Brand;

final class BrandTaxonomy
{
    public const TAXONOMY = 'product_brand';

    public function register(): void
    {
        add_action('init', function () {
            register_taxonomy(
                self::TAXONOMY,
                ['product'],
                [
                    'label'        => __('Brands', 'wcbh'),
                    'public'       => true,
                    'hierarchical' => false,
                    'rewrite'      => ['slug' => 'brand'],
                    'show_ui'      => true,
                    'show_admin_column' => true,
                ]
            );
        });
    }
}
