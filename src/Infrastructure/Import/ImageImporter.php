<?php

declare(strict_types=1);

namespace WCBH\Infrastructure\Import;

use WP_Error;

final class ImageImporter
{
    public static function import(string $url, string $title): ?int
    {
        if ($url === '') {
            return null;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        //Reuse attachment by source URL
        $existing = self::findBySourceUrl($url);
        if ($existing) {
            return $existing;
        }

        $parsed = wp_parse_url($url);
        $site   = wp_parse_url(site_url());

        $isLocal =
            empty($parsed['host']) ||
            in_array($parsed['host'], ['localhost', '127.0.0.1'], true) ||
            $parsed['host'] === ($site['host'] ?? null);

        // --------------------------------------------------
        // Local / same-site
        // --------------------------------------------------
        if ($isLocal) {
            $path = self::resolveLocalPath($url);

            if (!$path || !file_exists($path)) {
                return null;
            }

            $id = self::attachFromPath($path, $title);
        } else {
            // --------------------------------------------------
            // Remote
            // --------------------------------------------------
            $id = media_sideload_image($url, 0, $title, 'id');

            if ($id instanceof WP_Error) {
                return null;
            }
        }

        if ($id) {
            update_post_meta($id, '_wcbh_source_url', esc_url_raw($url));
        }

        return $id ? (int) $id : null;
    }

    private static function findBySourceUrl(string $url): ?int
    {
        $query = new \WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'   => '_wcbh_source_url',
                    'value' => esc_url_raw($url),
                ],
            ],
            'fields' => 'ids',
        ]);

        return $query->posts[0] ?? null;
    }

    private static function resolveLocalPath(string $url): ?string
    {
        $uploads = wp_get_upload_dir();

        if (str_starts_with($url, $uploads['baseurl'])) {
            return str_replace($uploads['baseurl'], $uploads['basedir'], $url);
        }

        $path = wp_parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        return ABSPATH . ltrim($path, '/');
    }

    private static function attachFromPath(string $path, string $title): ?int
    {
        $filetype = wp_check_filetype(basename($path));
        if (!$filetype['type']) {
            return null;
        }

        $attachment = [
            'post_mime_type' => $filetype['type'],
            'post_title'     => $title,
            'post_status'    => 'inherit',
        ];

        $id = wp_insert_attachment($attachment, $path);
        if (is_wp_error($id)) {
            return null;
        }

        $meta = wp_generate_attachment_metadata($id, $path);
        wp_update_attachment_metadata($id, $meta);

        return (int) $id;
    }
}
