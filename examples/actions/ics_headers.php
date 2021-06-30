<?php

/**
 * Add custom headers
 * @param WP_ICS_Feed $wp_ics_feed
 * @return void
 */
function my_ics_headers(WP_ICS_Feed $wp_ics_feed)
{
    header("Content-Description: File Transfer");
    header("Pragma: 0");
    header("Expires: 0");
}
add_action('ics_headers', 'my_ics_headers');
