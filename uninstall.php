<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Remove all brand term meta
$terms = get_terms([
    'taxonomy'   => 'wcbh_brand',
    'hide_empty' => false,
]);

foreach ($terms as $term) {
    delete_term_meta($term->term_id, 'wcbh_brand_banner');
}

// Remove the taxonomy itself
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'wcbh_brand'");
$wpdb->query("DELETE FROM {$wpdb->terms} WHERE term_id NOT IN (SELECT term_id FROM {$wpdb->term_taxonomy})");
