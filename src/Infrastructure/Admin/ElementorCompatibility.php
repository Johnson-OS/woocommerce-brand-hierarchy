<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Admin;

final class ElementorCompatibility
{
    public function register(): void
    {
        add_filter(
            'elementor/theme/location/show',
            [$this, 'forceProductArchiveHeader'],
            10,
            20
        );
    }

    public function forceProductArchiveHeader(bool $show, string $location): bool
    {
        if ($location !== 'archive') {
            return $show;
        }

        // Your brand pages condition
        if (get_query_var('wcbh_brand')) {
            return true;
        }

        return $show;
    }
}
