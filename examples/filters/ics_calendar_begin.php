<?php

/**
 * Filter BEGIN:VCALENDAR output
 * 
 * @param array $output
 * @param WP_ICS_Feed $wp_ics_feed
 * @return array $output
 */
function my_ics_calendar_begin(array $output, WP_ICS_Feed $wp_ics_feed)
{
    return $output;
}
add_filter('ics_calendar_begin', 'my_ics_calendar_begin', 10, 2);
