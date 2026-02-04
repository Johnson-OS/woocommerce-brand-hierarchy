<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Assets;

final class Assets
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontend']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdmin']);
    }

    public function enqueueFrontend(): void
    {
        wp_enqueue_style(
            'wcbh-frontend',
            WCBH_PLUGIN_URL . 'assets/css/styles.css',
            [],
            '1.0.0'
        );
    }

    public function enqueueAdmin(): void
    {
        wp_enqueue_media();

        wp_enqueue_script(
            'wcbh-admin',
            WCBH_PLUGIN_URL . 'assets/js/brand-banner.js',
            ['jquery'],
            '1.0.0',
            true
        );


        wp_enqueue_style(
            'wcbh-admin',
            WCBH_PLUGIN_URL . 'assets/css/styles.css',
            [],
            '1.0.0'
        );

        wp_localize_script('wcbh-term-meta', 'WCBH', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('wcbh_nonce'),
        ]);
    }
}
