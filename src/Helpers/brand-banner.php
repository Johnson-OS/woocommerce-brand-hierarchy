<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render brand banner for a product brand
 */
function wcbh_render_brand_banner(int $brandTermId): void
{
    $imageId = (int) get_term_meta($brandTermId, 'brand_banner_id', true);

    if (!$imageId) {
        return;
    }

    echo '<div class="wcbh-brand-banner wcbh-only-desktop">';
    echo wp_get_attachment_image(
        $imageId,
        'full',
        false,
        [
            'class' => 'wcbh-brand-banner-img',
            'loading' => 'lazy',
        ]
    );
    echo '</div>';
}
function wcbh_render_brand_thumbnail(int $brandTermId): void
{
    $thumbnail_id = get_term_meta( $brandTermId, 'thumbnail_id', true );

    if ( $thumbnail_id ) {
        echo wp_get_attachment_image(
            $thumbnail_id,
            'full',
            false,
            array( 'class' => 'wcbh-brand-thumbnail' )
        );

    }
}

