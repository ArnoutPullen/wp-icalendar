<?php

/**
 * Filter END:VCALENDAR output
 * 
 * @param array $output
 * @param WP_ICS_Feed $wp_ics_feed
 * @return array $output
 */
function ics_calendar_end(array $output, WP_ICS_Feed $wp_ics_feed)
{
    return $output;
}
add_filter('ics_calendar_end', 'ics_calendar_end', 10, 2);
