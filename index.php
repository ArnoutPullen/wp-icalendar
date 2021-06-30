<?php

/**
 * Plugin Name: WP iCalendar Plug-in
 * Description: Extends feed with ics
 * Version: 0.1
 * Author: Arnout Pullen
 * Author URI: https://arnoutpullen.nl
 */

defined('ABSPATH') || exit;

if (!defined('WP_ISC_FEED_PLUGIN_FILE')) {
    define('WP_ISC_FEED_PLUGIN_FILE', __FILE__);
}

// Include the WP_ICS_Plugin WebProfit class.
if (!class_exists('WP_ICS_Plugin', false)) {
    include_once dirname(WP_ISC_FEED_PLUGIN_FILE) . '/includes/class-wp-ics-plugin.php';
}
