<?php

/**
 * Filter WP_Query parameters
 * @link https://developer.wordpress.org/reference/classes/wp_query/
 * 
 * @param array $args
 * @param WP_ICS_Feed $wp_ics_feed
 * @return array $args
 */
function my_ics_get_posts_args(array $args, WP_ICS_Feed $wp_ics_feed)
{
    $args['post__not_in'] = [123, 456]; // Exclude posts

    return $args;
}
add_filter('ics_get_posts_args', 'my_ics_get_posts_args', 10, 2);
