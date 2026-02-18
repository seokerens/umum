<?php
require_once __DIR__ . '/wp-load.php';

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cache cleared.<br>";
}

if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "APCu cache cleared.<br>";
}

clearstatcache();
echo "Realpath cache cleared.<br>";

if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "WordPress object cache cleared.<br>";
}

global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_%'");
echo "WordPress transients cleared.<br>";

if (function_exists('w3tc_flush_all')) {
    w3tc_flush_all();
    echo "W3 Total Cache cache cleared.<br>";
}

if (function_exists('wp_cache_clear_cache')) {
    wp_cache_clear_cache();
    echo "WP Super Cache cache cleared.<br>";
}

if (function_exists('rocket_clean_domain')) {
    rocket_clean_domain();
    echo "WP Rocket cache cleared.<br>";
}

if (function_exists('wpfc_clear_all_cache')) {
    wpfc_clear_all_cache();
    echo "WP Fastest Cache cache cleared.<br>";
}

if (class_exists('LiteSpeed_Cache_API')) {
    LiteSpeed_Cache_API::purge_all();
    echo "LiteSpeed Cache cache cleared.<br>";
}

if (class_exists('autoptimizeCache')) {
    autoptimizeCache::clearall();
    echo "Autoptimize cache cleared.<br>";
}

if (function_exists('wphb_clear_page_cache')) {
    wphb_clear_page_cache();
    echo "Hummingbird cache cleared.<br>";
}

if (function_exists('ce_clear_cache')) {
    ce_clear_cache();
    echo "Cache Enabler cache cleared.<br>";
}

if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
    $sg = new SiteGround_Optimizer\Supercacher\Supercacher();
    $sg->purge_cache();
    echo "SiteGround Optimizer cache cleared.<br>";
}

if (class_exists('Kinsta\Cache')) {
    Kinsta\Cache::flush_cache();
    echo "Kinsta cache cleared.<br>";
}

echo "All cache cleared.<br>";