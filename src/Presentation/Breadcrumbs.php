<?php

declare(strict_types=1);

namespace WCBH\Presentation;

use WCBH\Domain\Brand\BrandTaxonomy;

final class Breadcrumbs
{
    public function register(): void
    {
        add_filter('woocommerce_get_breadcrumb', [$this, 'overrideWooBreadcrumbs'], 20);

    }

    public function overrideWooBreadcrumbs(array $crumbs): array
    {
        if (!is_product() && !get_query_var('wcbh_brand') && !is_shop()) {
            return $crumbs;
        }


        return $this->build();
    }

    private function build(): array
    {
        $breadcrumbs = [
            [__('Home','wcbh'), home_url('/')],
        ];

        // URL-based pages
        if ($brandSlug = get_query_var('wcbh_brand')) {
            $brand = get_term_by('slug', $brandSlug, BrandTaxonomy::TAXONOMY);
            if ($brand) {
                $breadcrumbs[] = [$brand->name, home_url("/brand/{$brand->slug}/")];
            }

            if ($catSlug = get_query_var('wcbh_cat')) {
                $cat = get_term_by('slug', $catSlug, 'product_cat');
                if ($cat) {
                    $breadcrumbs[] = [
                        $cat->name,
                        home_url("/brand/{$brand->slug}/{$cat->slug}/"),
                    ];
                }

                if ($subSlug = get_query_var('wcbh_subcat')) {
                    $sub = get_term_by('slug', $subSlug, 'product_cat');
                    if ($sub) {
                        $breadcrumbs[] = [
                            $sub->name,
                            home_url("/brand/{$brand->slug}/{$cat->slug}/{$sub->slug}/"),
                        ];
                    }
                }
            }

            return $breadcrumbs;
        }

        // Product page
        if (is_product()) {
            global $post;

            $brand = $this->getSingleTerm($post->ID, BrandTaxonomy::TAXONOMY);

            $terms = wp_get_object_terms($post->ID, 'product_cat');

            $cat = null;
            $sub    = null;

            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    if ($term->parent === 0) {
                        $cat = $term;
                    } else {
                        $sub = $term;
                    }
                }
            }

            if($cat ===null && $sub !== null) {
                $cat = get_term($sub->parent, 'product_cat');
            }

            if ($brand) {
                $breadcrumbs[] = [$brand->name, home_url("/brand/{$brand->slug}/")];
            }

            if ($brand && $cat) {
                $breadcrumbs[] = [
                    $cat->name,
                    home_url("/brand/{$brand->slug}/{$cat->slug}/"),
                ];
            }

            if ($brand && $cat && $sub) {
                $breadcrumbs[] = [
                    $sub->name,
                    home_url("/brand/{$brand->slug}/{$cat->slug}/{$sub->slug}/"),
                ];
            }

            $breadcrumbs[] = [get_the_title($post), ''];

            return $breadcrumbs;
        }

        return $breadcrumbs;
    }

    private function getSingleTerm(int $postId, string $taxonomy): ?\WP_Term
    {
        $terms = wp_get_object_terms($postId, $taxonomy);
        return $terms[0] ?? null;
    }

    public static function render(): void
    {
        $breadcrumbs = (new self())->build();

        if (empty($breadcrumbs)) {
            return;
        }

        echo '<nav class="woocommerce-breadcrumb">';
        foreach ($breadcrumbs as $index => [$label, $url]) {
            if ($index > 0) {
                echo ' <span class="delimiter">/</span> ';
            }

            if ($url) {
                printf('<a href="%s">%s</a>', esc_url($url), esc_html($label));
            } else {
                echo esc_html($label);
            }
        }
        echo '</nav>';
    }
}
