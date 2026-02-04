<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\QueryVars;

final class QueryVars
{
    public function register(): void
    {
        add_filter('query_vars', function (array $vars): array {
            $vars[] = 'wcbh_brand';
            $vars[] = 'wcbh_cat';
            $vars[] = 'wcbh_subcat';
            return $vars;
        });
    }
}
