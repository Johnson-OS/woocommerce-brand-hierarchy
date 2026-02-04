<?php

declare(strict_types=1);

namespace WCBH\Presentation;

final class TemplateLoader
{
    public function register(): void
    {
        add_filter('template_include', [$this, 'loadTemplate'], 20);
    }

    public function loadTemplate(string $template): string
    {
        if (!get_query_var('wcbh_brand')) {
            return $template;
        }

        $base = WCBH_PLUGIN_PATH . 'templates/brand/';

        if (get_query_var('wcbh_subcat')) {
            return $base . 'brand-subcategory.php';
        }

        if (get_query_var('wcbh_cat')) {
            return $base . 'brand-category.php';
        }

        return $base . 'archive-brand.php';
    }
}
