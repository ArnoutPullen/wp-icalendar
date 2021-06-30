<?php

defined('ABSPATH') || exit;

/**
 * 
 * https://icalendar.org/
 * https://en.wikipedia.org/wiki/iCalendar
 * https://www.kanzaki.com/docs/ical/
 */
class WP_ICS_Feed
{
    private $seperator = PHP_EOL;

    public function __construct()
    {
        if (isset($_GET['debug'])) {
            $this->seperator = "<br>";
        }
    }

    /**
     * Get HTTP headers
     */
    public function get_headers()
    {
        $post_type = $this->get_post_type('ical');

        header('Content-type: text/calendar');
        header('Content-Disposition: attachment; filename="' . $post_type . '.ics"');

        do_action('ics_headers', $this);
    }

    /**
     * Get calendar begin
     */
    public function get_calendar_begin_output()
    {
        $output = array(
            "BEGIN"   => "VCALENDAR",
            "VERSION" => "2.0",
            "PRODID"  => "-//" . $this->get_blog_name() . "//NONSGML WordPress iCalendar//" . $this->get_blog_language(),
            "TZID"    => $this->get_timezone_string(),
            "X-WR-CALNAME"  => $this->get_blog_name() . $this->get_post_type_name(' '),
            "X-WR-CALDESC"  => $this->get_blog_description(),
            "X-WR-TIMEZONE" => $this->get_timezone_string(),
        );

        // Customize calendar begin
        $output = apply_filters('ics_calendar_begin', $output, $this);

        // Convert array to output array
        $output = $this->array_to_output($output);

        return $output;
    }

    /**
     * Get calendar end
     */
    public function get_calendar_end_output()
    {
        $output = array(
            "END" => "VCALENDAR",
        );

        // Customize calendar end
        $output = apply_filters('ics_calendar_end', $output, $this);

        // Convert array to output array
        $output = $this->array_to_output($output);

        return $output;
    }

    /**
     * Get post type
     * @return string $post_type
     */
    public function get_post_type($post_type = 'any')
    {
        if (isset($_GET['post_type'])) {
            $get_post_type = $_GET['post_type'];
            if (post_type_exists($get_post_type)) {
                $post_type = $get_post_type;
            }
        }

        return $post_type;
    }

    /**
     * Get post type name
     * @param string $prefix
     * @return string
     */
    public function get_post_type_name($prefix = '')
    {
        $post_type_name = '';
        $post_type      = $this->get_post_type();
        $post_type_obj  = get_post_type_object($post_type);

        if (is_a($post_type_obj, 'WP_Post_type')) {
            $post_type_name = $post_type_obj->labels->name; // Events
            return $prefix . $post_type_name;
        }

        return $post_type_name;
    }

    /**
     * Get current blog id
     */
    public function get_current_blog_id()
    {
        return get_current_blog_id();
    }

    /**
     * Get blog name
     */
    public function get_blog_name()
    {
        return get_bloginfo('name');
    }

    /**
     * Get blog description
     */
    public function get_blog_description()
    {
        return get_bloginfo('description');
    }

    /**
     * Get blog language
     * @return string
     */
    public function get_blog_language()
    {
        return strtoupper(get_bloginfo("language"));
    }

    /**
     * Get Posts
     * @return array[WP_Post]
     */
    public function get_posts()
    {
        $posts = array();
        $args  = array(
            'post_type' => $this->get_post_type(),
            'posts_per_page' => -1,
        );

        $args  = apply_filters('ics_get_posts_args', $args, $this);
        $query = new WP_Query($args);

        if ($query->have_posts()) {
            $posts = $query->posts;
        }

        return $posts;
    }

    /**
     * Get Events
     * @return array
     */
    public function get_events()
    {
        $events = array();
        $posts  = $this->get_posts();

        /**
         * DTSTART;TZID=Europe/Amsterdam:20210801T093000
         * DTEND;TZID=Europe/Amsterdam:20210801T110000
         * DTSTAMP:20210629T081215Z
         * CATEGORIES:Kerkdiensten,Wijkgemeente Grote Kerk
         */
        foreach ($posts as $post) {
            $post_id = $post->ID;
            $uid = $this->get_current_blog_id() . '-' . $post_id;

            $event = array(
                'UID' => $uid,
                'URL' => get_permalink($post),
                'SUMMARY' => get_the_title($post),
                'DESCRIPTION' => html_entity_decode(trim(preg_replace('/\s\s+/', ' ', get_the_content(null, false, $post)))),
                'CREATED' => get_post_time('Ymd\THis\Z', true, $post_id),
                'LAST-MODIFIED' => get_the_modified_date('Ymd\THis\Z', $post_id),
                'DTSTAMP' => date_i18n('Ymd\THis\Z', time(), true),
            );

            // Customize event format
            $event = apply_filters('ics_event', $event, $post, $this);

            $events[] = $event;
        }

        return $events;
    }

    /**
     * Converts events in output array
     * @param string $this->seperator line seperator default PHP_EOL, can use <br> or \r\n
     * @return array
     */
    public function get_events_output($string = false)
    {
        $output = array();
        $events = $this->get_events();

        foreach ($events as $index => $event) {
            $output[] = 'BEGIN:VEVENT';
            foreach ($event as $key => $value) {
                $output[] = "$key:$value";
            }
            $output[] = 'END:VEVENT';
        }

        if ($string) {
            return implode($this->seperator, $output);
        }

        return $output;
    }

    public function get_output()
    {
        $output = array();

        if (!isset($_GET['debug'])) {
            $this->get_headers();
        }

        // Calendar begin
        array_push($output, ...(array)$this->get_calendar_begin_output());

        // Events
        array_push($output, ...(array)$this->get_events_output());

        // Calendar end
        array_push($output, ...(array)$this->get_calendar_end_output());

        // Customizable
        $output = apply_filters('ics_output', $output, $this);

        // End
        echo implode($this->seperator, $output);
    }

    /**
     * @return string
     */
    public function get_timezone_string()
    {
        return get_option('timezone_string');
        return wp_timezone_string();
    }

    /**
     * Convert array to output array
     * @param array $array
     * @return array $output
     */
    public function array_to_output($array)
    {
        $output = array();

        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $output[] = "$value";
            } else {
                $output[] = "$key:$value";
            }
        }

        return $output;
    }
}
