<?php
declare(strict_types=1);

namespace WCBH\Infrastructure\Import;

use WCBH\Domain\Brand\BrandTaxonomy;

final class BrandCsvImport
{
    private array $brandThumbnails = [];
    private array $brandBanners = [];

    public function register(): void
    {
        add_filter('woocommerce_csv_product_import_mapping_options', [$this, 'mapColumn']);
        add_filter('woocommerce_csv_product_import_mapping_default_columns', [$this, 'defaultColumn']);
        add_action('woocommerce_product_import_inserted_product_object', [$this, 'assignBrand'], 10, 2);
    }

    public function mapColumn(array $columns): array
    {
        $columns['wcbh_brand'] = __('WCBH Product Brand', 'wcbh');
        $columns['wcbh_brand_thumbnail'] = __('WCBH Brand Thumbnails', 'wcbh');
        $columns['wcbh_brand_banner'] = __('WCBH Brand Banners', 'wcbh');
        return $columns;
    }

    public function defaultColumn(array $columns): array
    {
        $columns['wcbh_brand'] = 'wcbh_brand';
        $columns['wcbh_brand_thumbnail'] = 'wcbh_brand_thumbnail';
        $columns['wcbh_brand_banner'] = 'wcbh_brand_banner';
        return $columns;
    }

    /**
     * Assign brand to product, import thumbnail/banner if defined
     */
    public function assignBrand($product, array $data): void
    {
        if (empty($data['wcbh_brand'])) {
            return;
        }

        $brandName = trim($data['wcbh_brand']);
        $brandSlug = sanitize_title($brandName);

        // Try to find existing term by slug
        $brand = term_exists($brandSlug, BrandTaxonomy::TAXONOMY);

        if (!$brand) {
            $brand = wp_insert_term($brandName, BrandTaxonomy::TAXONOMY, [
                'slug' => $brandSlug,
            ]);
        }

        if (is_wp_error($brand)) {
            return;
        }

        $brandId = (int) $brand['term_id'];

        // Assign product to brand (use ID, safest)
        wp_set_object_terms(
            $product->get_id(),
            [$brandId],
            BrandTaxonomy::TAXONOMY,
            false
        );

        // Process Thumbnail
        if (!empty($data['wcbh_brand_thumbnail'])) {
            $this->processBrandMediaColumn(
                $data['wcbh_brand_thumbnail'],
                'thumbnail_id',
                $brandId
            );
        }

        // Process Banner
        if (!empty($data['wcbh_brand_banner'])) {
            $this->processBrandMediaColumn(
                $data['wcbh_brand_banner'],
                'brand_banner_id',
                $brandId
            );
        }
    }

    /**
     * Parse brand:URL pairs and import images
     *
     * @param string $columnData
     * @param string $metaKey 'thumbnail_id' or 'brand_banner_id'
     */
    private function processBrandMediaColumn(string $columnData, string $metaKey): void
    {
        $pairs = explode(',', $columnData);
        foreach ($pairs as $pair) {
            [$brandName, $url] = array_map('trim', explode('|', $pair, 2));
            if (empty($brandName) || empty($url)) continue;

            $brandSlug = sanitize_title($brandName);
            $term = term_exists($brandName, BrandTaxonomy::TAXONOMY);

            if (!$term) {
                $term = wp_insert_term($brandName, BrandTaxonomy::TAXONOMY);
            }

            if (is_wp_error($term)) {
                continue;
            }

            $brandId = (int) $term['term_id'];

            // Avoid re-importing the same URL multiple times
            if ($metaKey === 'thumbnail_id' && get_term_meta($brandId, 'thumbnail_id', true)) continue;
            if ($metaKey === 'brand_banner' && get_term_meta($brandId, 'brand_banner_id', true)) continue;

            $attachmentId = ImageImporter::import($url, $brandSlug . ' ' . $metaKey);
            if ($attachmentId) {
                update_term_meta($brandId, $metaKey, $attachmentId);
            }
        }
    }
}
