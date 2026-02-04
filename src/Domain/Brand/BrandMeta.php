<?php

declare(strict_types=1);

namespace WCBH\Domain\Brand;


final class BrandMeta
{
    public function register(): void {

        add_action(BrandTaxonomy::TAXONOMY.'_add_form_fields',[$this, 'addBrandImageForm']);
        add_action(BrandTaxonomy::TAXONOMY.'_edit_form_fields',[$this, 'editBrandImageForm']);
        add_action('created_'.BrandTaxonomy::TAXONOMY, [$this, 'wcbh_save_brand_banner']);
        add_action('edited_'.BrandTaxonomy::TAXONOMY, [$this, 'wcbh_save_brand_banner']);
    }

    public function addBrandImageForm(): void
    {
        ?>
        <div class="form-field">
            <label><?php esc_html_e('Brand Banner', 'wcbh'); ?></label>
            <input type="hidden" id="brand_banner_id" name="brand_banner_id" value="">
            <button class="button wcbh-upload-banner">
                <?php esc_html_e('Upload Banner', 'wcbh'); ?>
            </button>
            <div class="wcbh-banner-preview">

            </div>
        </div>
        <?php
    }

    public function editBrandImageForm($term): void
    {
        $banner_id = get_term_meta($term->term_id, 'brand_banner_id', true);
        ?>
        <tr class="form-field">
            <th scope="row">
                <label><?php esc_html_e('Brand Banner', 'wcbh'); ?></label>
            </th>
            <td>
                <input type="hidden" id="brand_banner_id" name="brand_banner_id"
                       value="<?= esc_attr($banner_id); ?>">

                <button class="button wcbh-upload-banner">
                    <?php esc_html_e('Upload Banner', 'wcbh'); ?>
                </button>

                <div class="wcbh-banner-preview">
                    <?php
                    if ($banner_id) {
                        echo wp_get_attachment_image($banner_id, 'large');
                    }
                    ?>
                </div>
            </td>
        </tr>
        <?php
    }

    function wcbh_save_brand_banner(int $term_id): void
    {
        if (isset($_POST['brand_banner_id'])) {
            update_term_meta(
                $term_id,
                'brand_banner_id',
                (int) $_POST['brand_banner_id']
            );
        }
    }
}
