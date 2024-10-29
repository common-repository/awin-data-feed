<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

global $wpdb;
$table = $wpdb->prefix . "datafeed";
$tableAnalytics = $wpdb->prefix . "datafeed_analytics";

delete_option('sw_deliveryMethod');
delete_option('sw_categories');
delete_option('sw_maxPriceRadio');
delete_option('sw_minPrice');
delete_option('sw_maxPrice');

$wpdb->query("DROP TABLE IF EXISTS $table");
$wpdb->query("DROP TABLE IF EXISTS $tableAnalytics");
