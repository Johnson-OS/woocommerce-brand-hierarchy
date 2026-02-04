<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Admin;

final class ProductBrandSortable
{
    public function register(): void
    {
        add_filter('manage_edit-product_sortable_columns', [$this, 'sortable']);
    }

    public function sortable(array $columns): array
    {
        $columns['wcbh_brand'] = 'brand';
        return $columns;
    }
}
