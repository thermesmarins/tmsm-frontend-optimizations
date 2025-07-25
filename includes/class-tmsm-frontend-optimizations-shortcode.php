<?php

use Elementor\Plugin;

/**
 * This File is responsible to handle shortcodes within the plugin.
 */

class Tmsm_Frontend_Optimizations_Shortcode
{
    public function __construct()
    {

        add_shortcode('rss-with-image', array($this, 'rss_output_gifts'));
        add_shortcode('rss-with-image-activities', array($this, 'rss_output_activities'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    /**
     * Display the RSS entries in a list with image.
     *
     * @param array $atts
     *
     * @since 2.5.0
     *
     */
    function rss_output_gifts(array $atts = [])
    {

        $atts = shortcode_atts(array(
            'rss'          => '',
            'show_author'  => 0,
            'show_summary' => 0,
            'show_date'    => 0,
            'show_media'   => 0,
            'show_price'   => 0,
            'items'        => 0,
            'columns'      => 3,
            'button_show'  => true,
            'button_text'  => 'Voir la prestation',
        ), $atts, 'rss-with-image');

        $rss = $atts['rss'];
        $columns = $atts['columns'];

        if (is_string($rss)) {
            $rss = fetch_feed($rss);
        } elseif (is_array($rss) && isset($rss['url'])) {
            $args = $rss;
            $rss  = fetch_feed($rss['url']);
        } elseif (! is_object($rss)) {
            return;
        }

        if (is_wp_error($rss)) {
            if (is_admin() || current_user_can('manage_options')) {
                echo '<p><strong>' . __('RSS Error:') . '</strong> ' . $rss->get_error_message() . '</p>';
            }

            return;
        }

        $default_args = array(
            'show_author'  => 0,
            'show_date'    => 0,
            'show_summary' => 0,
            'show_media'   => 0,
            'show_price'   => 0,
            'items'        => 0,
            'button_show'  => true,
            'button_text'  => 'Voir la prestation',
        );
        $args         = wp_parse_args($atts, $default_args);

        $items = (int) $args['items'];
        if ($items < 1 || 20 < $items) {
            $items = 10;
        }
        $show_summary = (bool) $args['show_summary'];
        $show_author  = (bool) $args['show_author'];
        $show_date    = (bool) $args['show_date'];
        $show_media   = (bool) $args['show_media'];
        $show_price   = (bool) $args['show_price'];
        $button_show = (bool) $args['button_show'];
        $paddingbottom = 80;
        $button_text = $args['button_text'];
        if (! $rss->get_item_quantity()) {
            echo '<ul><li>' . __('An error has occurred, which probably means the feed is down. Try again later.') . '</li></ul>';
            $rss->__destruct();
            unset($rss);

            return;
        }

        // echo '<div class="rss-with-image" style="    display: grid;  grid-template-columns: repeat('.esc_attr($columns).', 1fr);">';
        // Css dans le style de Elementor
            echo '<div class="rss-with-image-gifts">';

        foreach ($rss->get_items(0, $items) as $item) {

            $link = $item->get_link();
            while (! empty($link) && stristr($link, 'http') !== $link) {
                $link = substr($link, 1);
            }
            $link = esc_url(strip_tags($link));

            $title = '<h4 class="text-center" style="text-align: center">' . esc_html(trim(strip_tags($item->get_title()))) . '</h4>';
            if (empty($title)) {
                $title = __('Untitled');
            }

            $summary = '';
            $desc = $item->get_description();
            if ($show_summary) {
                $summary = $desc;
                // Change existing [...] to [&hellip;].
                if ('[...]' === substr($summary, -5)) {
                    $summary = substr($summary, 0, -5) . '[&hellip;]';
                }
                $summary = '<div class="summary">' . ($summary) . '</div>';
            } 

            $date = '';
            if ($show_date) {
                $date = $item->get_date('U');
                if ($date) {
                    $date = ' <span class="rss-date">' . date_i18n(get_option('date_format'), $date) . '</span>';
                }
            }

            $author = '';
            if ($show_author) {
                $author = $item->get_author();
                if (is_object($author)) {
                    $author = $author->get_name();
                    $author = ' <cite>' . esc_html(strip_tags($author)) . '</cite>';
                }
            }

            $thumbnail = '';
            $title_text = esc_html(trim(strip_tags($item->get_title())));
            $thumbnail = '';
            if ($show_media) {
                    // 1. Essayer enclosure
                    if ($enclosure = $item->get_enclosure()) {
                        $thumbnail = $enclosure->get_link();
                    }
                    // 2. Essayer balise <enclosure>
                    if (empty($thumbnail)) {
                        $media = $item->get_item_tags('', 'enclosure');
                        if (!empty($media) && isset($media[0]['attribs']['']['url'])) {
                            $thumbnail = $media[0]['attribs']['']['url'];
                        }
                    }
                    // 3. Si toujours rien, extraire la première image du contenu
                    if (empty($thumbnail)) {
                        $desc = $item->get_content();
                        if (empty($desc)) {
                            $desc = $item->get_description();
                        }
                        if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $desc, $matches)) {
                            $thumbnail = $matches[1];
                        }
                    }
                    if ($thumbnail) {
                        $thumbnail = '<div class="thumbnail"><img src="' . esc_url($thumbnail) . '" alt="' . $title_text . '"/></div>';
                    }
            }

            $price = '';
            if ($enclosure = $item->get_enclosure()) {
                foreach ((array) $enclosure->get_ratings() as $enclosure_price) {
                    $price =  $enclosure_price->get_value();
                }
                if ($price) {
                    $paddingbottom = 140;
                    $price = '<div class="rss-item-price" style="text-align:center; position: absolute;    bottom: 100px;right: 0;    left: 0;">' . ($price) . '</div>';
                }
            }

            $button = '';
            if ($button_show === true) {
                $button = '<p class="rss-item-button" style="text-align:center; position: absolute;    bottom: 20px;right: 0;    left: 0;" ><a class="button btn btn-primary" href="' . esc_attr($link) . '">' . __($button_text, 'nouveaumonde')
                    . '</a></p>';
            }

            echo "<div class='rss-item' style=\" flex: 1 1 33%; padding:0 10px " . $paddingbottom . "px 10px; position: relative\" >{$thumbnail}{$title}{$date}{$summary}{$author}{$price}{$button}</div>";
        }
        echo '</div>';
        $rss->__destruct();
        unset($rss);
    }

    /**
     * Enqueue CSS styles for RSS activities
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            'tmsm-rss-activities-style',
            plugin_dir_url(__FILE__) . '../assets/css/rss-shortcodes.css',
            array(),
            '1.0.0'
        );
    }

    function rss_output_activities(array $atts = [])
    {
        $atts = shortcode_atts(array(
            'rss'          => '',
            'show_author'  => 0,
            'show_summary' => 0,
            'show_date'    => 0,
            'show_media'   => 0,
            'columns'      => 3, // 0 = auto, 1 = 1, 2 = 2, 3 = 3, 4 = 4, 5 = 5, 6 = 6, 7 = 7, 8 = 8, 9 = 9, 10 = 10
            'in_lines'     => 0, // 0 = grid, 1 = lines
            'button_show'  => true,
            'description_length' => 0,
            'button_text'  => 'En savoir plus',
        ), $atts, 'rss-with-image-activities');
        $default_args = array(
            'rss'          => '',
            'show_author'  => 0,
            'show_summary' => 0,
            'show_date'    => 0,
            'items'        => 0,
            'show_media'   => 0,
            'columns'      => 0, // 1 = 1, 2 = 2, 3 = 3, 4 = 4, 5 = 5, 6 = 6, 7 = 7, 8 = 8, 9 = 9, 10 = 10
            'in_lines'     => 1, // 0 = grid, 1 = lines
            'button_show'  => true,
            'description_length' => 0,
            'button_text'  => 'En savoir plus',
        );
        $rss = $atts['rss'];
        $columns = $atts['columns'];
        $in_lines = $atts['in_lines'];
        if (is_string($rss)) {
            $rss = fetch_feed($rss);
        } elseif (is_array($rss) && isset($rss['url'])) {
            $args = $rss;
            $rss  = fetch_feed($rss['url']);
        } elseif (! is_object($rss)) {
            return;
        }
        if (is_wp_error($rss)) {
            if (is_admin() || current_user_can('manage_options')) {
                echo '<p><strong>' . __('RSS Error:') . '</strong> ' . $rss->get_error_message() . '</p>';
            }
            return;
        }
        $default_args = array(
            'show_author'  => 0,
            'show_date'    => 0,
            'show_summary' => 0,
            'items'        => 0,
            'description_length' => 50,
            'button_show'  => true,
            'button_text'  => 'En savoir plus',
        );
        $args         = wp_parse_args($atts, $default_args);
        $items = (int) $args['items'];
        if ($items < 1 || 20 < $items) {
            $items = 10;
        }
        $show_summary = (bool) $args['show_summary'];
        $paddingbottom = 80;
        $show_author  = (bool) $args['show_author'];
        $show_date    = (bool) $args['show_date'];
        $show_media   = (bool) $args['show_media'];
        $button_show = (bool) $args['button_show'];
        $button_text = $args['button_text'];
        $description_length = intval($args['description_length']);
        if (! $rss->get_item_quantity()) {
            echo '<ul><li>' . __('An error has occurred, which probably means the feed is down. Try again later.') . '</li></ul>';
            $rss->__destruct();
            unset($rss);

            return;
        }
        echo '<div class="rss-activities-with-image">';
        foreach ($rss->get_items(0, $items) as $item) {
            $link = $item->get_link();
            while (! empty($link) && stristr($link, 'http') !== $link) {
                $link = substr($link, 1);
            }
            $link = esc_url(strip_tags($link));
            $title = '<h4>' . esc_html(trim(strip_tags($item->get_title()))) . '</h4>';
            if (empty($title)) {
                $title = __('Untitled');
            }
            $summary = '';
            // Récupérer le contenu complet depuis content:encoded en priorité
            $desc = $item->get_content();
            if (empty($desc)) {
                $desc = $item->get_description();
            }
            if ($description_length > 0) {
                $summary = (wp_trim_words($desc, $description_length, ' [&hellip;]'));
                $summary = '<div class="summary">' . ($summary) . '</div>';
            } elseif ($description_length === 0) {
                // Afficher le contenu complet sans troncature
                $summary = $desc;
                $summary = '<div class="summary">' . ($summary) . '</div>';
            }
            $date = '';
            if ($show_date) {
                $date = $item->get_date('U');
                if ($date) {
                    $date = '<span class="rss-date">' . date_i18n(get_option('date_format'), $date) . '</span>';
                }
            }
            $author = '';
            if ($show_author) {
                $author = $item->get_author();
                if (is_object($author)) {
                    $author = $author->get_name();
                    $author = '<cite>' . esc_html(strip_tags($author)) . '</cite>';
                }
            }
            $thumbnail = '';
            $title_text = esc_html(trim(strip_tags($item->get_title())));
            if ($show_media) {
                // 1. Essayer enclosure
                if ($enclosure = $item->get_enclosure()) {
                    $thumbnail = $enclosure->get_link();
                }
                // 2. Essayer balise <enclosure>
                if (empty($thumbnail)) {
                    $media = $item->get_item_tags('', 'enclosure');
                    if (!empty($media) && isset($media[0]['attribs']['']['url'])) {
                        $thumbnail = $media[0]['attribs']['']['url'];
                    }
                }
                // 3. Si toujours rien, extraire la première image du contenu
                if (empty($thumbnail)) {
                    $desc = $item->get_content();
                    if (empty($desc)) {
                        $desc = $item->get_description();
                    }
                    if (preg_match('/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $desc, $matches)) {
                        $thumbnail = $matches[1];
                    }
                }
                if ($thumbnail) {
                    $thumbnail = '<div class="thumbnail"><img src="' . esc_url($thumbnail) . '" alt="' . $title_text . '"/></div>';
                }
            }
            $button = '';
            if ($button_show === true) {
                $button = '<p class="rss-item-button"><a class="elementor-button elementor-button-link" href="' . esc_attr($link) . '">' . __($button_text, 'nouveaumonde')
                    . '</a></p>';
            }

            echo '<div class="rss-item">';
            echo $thumbnail;
            echo '<div class="rss-content">';
            echo '<div class="rss-main-content">';
            echo $title . $date . $summary . $author;
            echo '</div>';
            echo $button;
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
}
