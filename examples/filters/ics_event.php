<?php

/**
 * Filter events output
 * 
 * @param array $event
 * @param WP_Post $post
 * @param WP_ICS_Feed $wp_ics_feed
 * @return array $event
 */
function my_ics_event(array $event, WP_Post $post, WP_ICS_Feed $wp_ics_feed)
{
    $timezone = $wp_ics_feed->get_timezone_string();

    if ($post->post_type == 'agenda') {
        $start_timestamp = types_render_field('datetime', array('post_id' => $post->ID, 'format' => 'U', 'raw' => true));
        $end_timestamp   = types_render_field('datetime', array('post_id' => $post->ID, 'format' => 'U', 'raw' => true));

        $start_datetime = date_i18n("Ymd\THis\Z", $start_timestamp);
        $end_datetime   = date_i18n("Ymd\THis\Z", $end_timestamp);

        if ($start_timestamp == $end_timestamp) {
            $end_datetime = date_i18n("Ymd\THis\Z", $start_timestamp + (1 * 60 * 60)); // + 1 hour
        }

        if (!empty($start_datetime)) {
            $event['DTSTART;TZID=' . $timezone] = $start_datetime;
        }

        if (!empty($end_datetime)) {
            $event['DTEND;TZID=' . $timezone] = $end_datetime;
        }
    }

    return $event;
}
add_filter('ics_event', 'my_ics_event', 10, 3);
