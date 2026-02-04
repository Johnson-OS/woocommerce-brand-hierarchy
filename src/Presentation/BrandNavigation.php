<?php
namespace WCBH\Presentation;

use WP_Term;

final class BrandNavigation
{
    public static function categoriesForBrand(string $brandSlug, int $parent = 0): array
    {
        $products = get_posts([
            'post_type' => 'product',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'tax_query' => [[
                'taxonomy' => 'product_brand',
                'field' => 'slug',
                'terms' => $brandSlug,
            ]],
        ]);

        if (!$products) return [];

        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'object_ids' => $products,
        ]);

        $categories = [];

        foreach ($terms as $term) {
            // Only include top-level categories (parent == 0)
            $parent_id = ($term->parent != 0) ? $term->parent : $term->term_id;

            if (!isset($categories[$parent_id])) {
                $parent = get_term($parent_id, 'product_cat');
                if ($parent && !is_wp_error($parent)) {
                    $thumbnail_id = get_term_meta($parent->term_id, 'thumbnail_id', true);
                    $parent->image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
                    /*$parent->product_count = self::countProducts(
                        $brandSlug,
                        $term->term_id
                    );

                    $parent->product_count += self::countProductsIncludingSubcategories(
                        $brandSlug,
                        $term->term_id
                    );*/

                    $categories[$parent->term_id] = $parent;
                }
            }
        }

        return array_values($categories);

    }

    /**
     * Get subcategories of a parent category for a specific brand
     *
     * @param string $brandSlug  The brand slug
     * @param int    $parentId   The parent category ID
     * @return array             Array of WP_Term objects with extra fields
     */
    public static function subcategoriesForBrand(string $brandSlug, int $parentId): array
    {
        $products = get_posts([
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => [[
                'taxonomy' => 'product_brand',
                'field'    => 'slug',
                'terms'    => $brandSlug,
            ]],
        ]);

        if (!$products) {
            return [];
        }

        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'object_ids' => $products,
            'parent'     => $parentId,
        ]);


        foreach ($terms as $term) {
            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            $term->image_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';
            //$term->product_count = self::countProducts($brandSlug, $term->term_id);
        }

        return $terms;
    }

    public static function countProducts($brandSlug, int $categoryId): int
    {
        $q = new \WP_Query([
            'post_type'      => 'product',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'tax_query'      => [
                'relation' => 'AND',
                [
                    'taxonomy' => 'product_brand',
                    'field'    => 'slug',
                    'terms'    => $brandSlug,
                ],
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $categoryId,
                ],
            ],
        ]);

        return (int) $q->found_posts;
    }

    /**
     * Count all products of a brand in a category including subcategories
     *
     * @param string $brandSlug   Brand slug
     * @param int    $categoryId  Parent category ID
     * @return int
     */
    public static function countProductsIncludingSubcategories(string $brandSlug, int $categoryId): int
    {

        $childTerms = self::subcategoriesForBrand($brandSlug, $categoryId);

        $count = 0;

        foreach ($childTerms as $term) {
            $count += $term->product_count;
        }

        return $count;
    }


}
