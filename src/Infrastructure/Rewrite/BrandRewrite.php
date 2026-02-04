<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Rewrite;

final class BrandRewrite
{
    public function register(): void
    {
        add_action('init', [$this, 'addRules']);
    }

    public function addRules(): void
    {
        add_rewrite_rule(
            '^brand/([^/]+)/?$',
            'index.php?wcbh_brand=$matches[1]',
            'top'
        );

        add_rewrite_rule(
            '^brand/([^/]+)/([^/]+)/?$',
            'index.php?wcbh_brand=$matches[1]&wcbh_cat=$matches[2]',
            'top'
        );

        add_rewrite_rule(
            '^brand/([^/]+)/([^/]+)/([^/]+)/?$',
            'index.php?wcbh_brand=$matches[1]&wcbh_cat=$matches[2]&wcbh_subcat=$matches[3]',
            'top'
        );
    }
}
