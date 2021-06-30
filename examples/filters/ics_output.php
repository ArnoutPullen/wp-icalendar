<?php

/**
 * Filter iCalendar output
 * 
 * @param array $output
 * @param WP_ICS_Feed $wp_ics_feed
 * @return array $output
 */
function my_ics_output(array $output, WP_ICS_Feed $wp_ics_feed)
{
    $new_output = [];

    foreach ($output as $item) {

        // Change Product Identifier
        if (str_starts_with($item, "PRODID:")) {
            $item = "PRODID:-//ABC Corporation//NONSGML My Product//EN";
        }

        // Add calendar name
        if ($item === "BEGIN:VCALENDAR") {
            $new_output[] = "X-WR-CALNAME:My Custom Calendar";
        }

        $new_output[] = $item;
    }

    return $new_output;
}
add_filter('ics_output', 'my_ics_output', 10, 3);
