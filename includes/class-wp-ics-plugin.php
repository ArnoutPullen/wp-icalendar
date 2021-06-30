<?php

defined('ABSPATH') || exit;

class WP_ICS_Plugin
{
    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    public function define_constants()
    {
        define('WP_ISC_FEED_PLUGIN_DIR', plugin_dir_path(WP_ISC_FEED_PLUGIN_FILE));
        define('WP_ISC_FEED_PLUGIN_URL', plugin_dir_url(WP_ISC_FEED_PLUGIN_FILE));
    }

    public function includes()
    {
        include WP_ISC_FEED_PLUGIN_DIR . 'includes/class-wp-ics-feed.php';
    }

    public function init_hooks()
    {
        add_action('init', [$this, 'register_feeds']);
        register_activation_hook(WP_ISC_FEED_PLUGIN_FILE, [$this, 'register_activation_hook']);
        register_deactivation_hook(WP_ISC_FEED_PLUGIN_FILE, [$this, 'register_deactivation_hook']);
    }

    public function register_activation_hook()
    {
        flush_rules();
    }

    public function register_deactivation_hook()
    {
        flush_rules();
    }

    public function register_feeds()
    {
        add_feed('ics', [$this, 'ics_feed_callback']);
    }

    public function ics_feed_callback()
    {
        $wp_ics_feed = new WP_ICS_Feed();
        $wp_ics_feed->get_output();
    }
}
